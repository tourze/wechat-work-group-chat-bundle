<?php

namespace WechatWorkGroupChatBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Messenger\MessageBusInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use Tourze\WechatWorkContracts\UserInterface;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkServiceInterface;
use WechatWorkExternalContactBundle\Request\GetFollowUserListRequest;
use WechatWorkGroupChatBundle\Entity\GroupChat;
use WechatWorkGroupChatBundle\Enum\GroupChatStatus;
use WechatWorkGroupChatBundle\Message\SyncGroupChatDetailMessage;
use WechatWorkGroupChatBundle\Repository\GroupChatRepository;
use WechatWorkGroupChatBundle\Request\GetGroupChatListRequest;

/**
 * @see https://developer.work.weixin.qq.com/document/path/92120
 */
#[AsCronTask(expression: '14 6 * * *')]
#[AsCommand(name: self::NAME, description: '同步客户群数据到本地')]
#[Autoconfigure(public: true)]
class SyncGroupChatListCommand extends Command
{
    public const NAME = 'wechat-work:sync-group-chat-list';

    public function __construct(
        private readonly WorkServiceInterface $workService,
        private readonly UserLoaderInterface $userLoader,
        private readonly GroupChatRepository $groupChatRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly EntityManagerInterface $entityManager,
        private readonly AgentRepository $agentRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $agents = $this->agentRepository->findAll();
        foreach ($agents as $agent) {
            $this->syncGroupChatsForAgent($agent);
        }

        return Command::SUCCESS;
    }

    private function syncGroupChatsForAgent(Agent $agent): void
    {
        $userListRequest = new GetFollowUserListRequest();
        $userListRequest->setAgent($agent);
        $userListResponse = $this->workService->request($userListRequest);

        if (!is_array($userListResponse) || !isset($userListResponse['follow_user']) || !is_array($userListResponse['follow_user'])) {
            return;
        }

        foreach ($userListResponse['follow_user'] as $userId) {
            if (is_string($userId)) {
                $this->syncGroupChatsForUser($userId, $agent);
            }
        }
    }

    private function syncGroupChatsForUser(string $userId, Agent $agent): void
    {
        $corp = $agent->getCorp();
        if (null === $corp) {
            return;
        }

        $user = $this->userLoader->loadUserByUserIdAndCorp($userId, $corp);
        if (null === $user) {
            return;
        }

        $cursor = null;
        do {
            $cursor = $this->syncGroupChatsBatch($user, $agent, $cursor);
        } while (null !== $cursor);
    }

    private function syncGroupChatsBatch(UserInterface $user, Agent $agent, ?string $cursor): ?string
    {
        $request = new GetGroupChatListRequest();
        $request->setAgent($agent);
        $userId = $user->getUserId();
        if (null !== $userId) {
            $request->setOwnerUserIds([$userId]);
        }
        $request->setStatusFilter(0);
        $request->setLimit(100);

        if (null !== $cursor) {
            $request->setCursor($cursor);
        }

        $response = $this->workService->request($request);

        if (!is_array($response)) {
            return null;
        }

        $groupChatList = $response['group_chat_list'] ?? [];
        if (is_array($groupChatList)) {
            $this->processGroupChatList($groupChatList, $agent);
        }

        $nextCursor = $response['next_cursor'] ?? null;

        return is_string($nextCursor) ? $nextCursor : null;
    }

    /**
     * @param array<mixed> $groupChatList
     */
    private function processGroupChatList(array $groupChatList, Agent $agent): void
    {
        foreach ($groupChatList as $item) {
            if (is_array($item)) {
                $this->syncGroupChat($item, $agent);
            }
        }
    }

    /**
     * @param array<mixed> $item
     */
    private function syncGroupChat(array $item, Agent $agent): void
    {
        if (!isset($item['chat_id']) || !is_string($item['chat_id'])) {
            return;
        }

        $chatId = $item['chat_id'];
        $group = $this->groupChatRepository->findOneBy([
            'chatId' => $chatId,
        ]);

        if (!$group instanceof GroupChat) {
            $group = new GroupChat();
            $group->setChatId($chatId);
        }

        $group->setAgent($agent);
        $group->setCorp($agent->getCorp());

        if (isset($item['status']) && (is_int($item['status']) || is_string($item['status']))) {
            $status = GroupChatStatus::tryFrom($item['status']);
            $group->setStatus($status);
        }

        $this->entityManager->persist($group);
        $this->entityManager->flush();

        $this->dispatchSyncDetailMessage($chatId);
    }

    private function dispatchSyncDetailMessage(string $chatId): void
    {
        $message = new SyncGroupChatDetailMessage();
        $message->setChatId($chatId);
        $this->messageBus->dispatch($message);
    }
}
