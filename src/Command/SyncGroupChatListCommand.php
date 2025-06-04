<?php

namespace WechatWorkGroupChatBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkExternalContactBundle\Enum\GroupChatStatus;
use WechatWorkExternalContactBundle\Request\GetFollowUserListRequest;
use WechatWorkGroupChatBundle\Entity\GroupChat;
use WechatWorkGroupChatBundle\Message\SyncGroupChatDetailMessage;
use WechatWorkGroupChatBundle\Repository\GroupChatRepository;
use WechatWorkGroupChatBundle\Request\GetGroupChatListRequest;

/**
 * @see https://developer.work.weixin.qq.com/document/path/92120
 */
#[AsCronTask('14 6 * * *')]
#[AsCommand(name: 'wechat-work:SyncGroupChatListCommand', description: '同步客户群数据到本地')]
class SyncGroupChatListCommand extends Command
{
    public function __construct(
        private readonly AgentRepository $agentRepository,
        private readonly WorkService $workService,
        private readonly UserLoaderInterface $userLoader,
        private readonly GroupChatRepository $groupChatRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->agentRepository->findAll() as $agent) {
            $userListRequest = new GetFollowUserListRequest();
            $userListRequest->setAgent($agent);
            $userListResponse = $this->workService->request($userListRequest);
            if (!isset($userListResponse['follow_user'])) {
                continue;
            }

            foreach ($userListResponse['follow_user'] as $userId) {
                $user = $this->userLoader->loadUserByUserIdAndCorp($userId, $agent->getCorp());
                if (!$user) {
                    continue;
                }

                $cursor = null;

                do {
                    $request = new GetGroupChatListRequest();
                    $request->setAgent($agent);
                    $request->setOwnerUserIds([$user->getUserId()]);
                    $request->setStatusFilter(0);
                    $request->setLimit(100);
                    if (null !== $cursor) {
                        $request->setCursor($cursor);
                    }

                    $response = $this->workService->request($request);
                    if (isset($response['next_cursor'])) {
                        $cursor = $response['next_cursor'];
                    } else {
                        $cursor = null;
                    }

                    foreach ($response['group_chat_list'] as $item) {
                        $group = $this->groupChatRepository->findOneBy([
                            'chatId' => $item['chat_id'],
                        ]);
                        if (!$group) {
                            $group = new GroupChat();
                            $group->setChatId($item['chat_id']);
                        }
                        $group->setAgent($agent);
                        $group->setCorp($agent->getCorp());
                        $group->setStatus(GroupChatStatus::tryFrom($item['status']));
                        $this->entityManager->persist($group);
                        $this->entityManager->flush();
                        // 这里只保存了基础信息，相信信息还需要定时任务去同步

                        $message = new SyncGroupChatDetailMessage();
                        $message->setChatId($group->getChatId());
                        $this->messageBus->dispatch($message);
                    }
                } while (null !== $cursor);
            }
        }

        return Command::SUCCESS;
    }
}
