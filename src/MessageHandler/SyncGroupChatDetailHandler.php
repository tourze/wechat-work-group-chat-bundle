<?php

namespace WechatWorkGroupChatBundle\MessageHandler;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Service\WorkService;
use WechatWorkGroupChatBundle\Entity\GroupMember;
use WechatWorkGroupChatBundle\Message\SyncGroupChatDetailMessage;
use WechatWorkGroupChatBundle\Repository\GroupChatRepository;
use WechatWorkGroupChatBundle\Request\GetGroupChatDetailRequest;

/**
 * @see https://developer.work.weixin.qq.com/document/path/92122
 */
#[AsMessageHandler]
class SyncGroupChatDetailHandler
{
    public function __construct(
        private readonly GroupChatRepository $groupChatRepository,
        private readonly WorkService $workService,
        private readonly UserLoaderInterface $userLoader,
        #[Autowire(service: 'wechat-work-external-contact-bundle.property-accessor')] private readonly PropertyAccessor $propertyAccessor,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(SyncGroupChatDetailMessage $message): void
    {
        $group = $this->groupChatRepository->findOneBy(['chatId' => $message->getChatId()]);
        if (null === $group) {
            throw new UnrecoverableMessageHandlingException('数据库中找不到客户群信息');
        }

        $request = new GetGroupChatDetailRequest();
        $request->setAgent($group->getAgent());
        $request->setChatId($group->getChatId());
        $request->setNeedName(true);
        $response = $this->workService->request($request);
        if (!isset($response['group_chat'])) {
            throw new UnrecoverableMessageHandlingException('接口中找不到客户群信息');
        }

        $group->setName($response['group_chat']['name']);
        $group->setNotice($response['group_chat']['notice']);
        $group->setCreateTime(Carbon::createFromTimestamp($response['group_chat']['create_time'], date_default_timezone_get()));

        // 拥有者
        $user = $this->userLoader->loadUserByUserIdAndCorp($response['group_chat']['owner'], $group->getCorp());
        if (null !== $user) {
            $group->setOwner($user);
        }

        // 群管理员列表
        $admins = [];
        foreach ($response['group_chat']['admin_list'] as $item) {
            $user = $this->userLoader->loadUserByUserIdAndCorp($item['userid'], $group->getCorp());
            if (null === $user) {
                continue;
            }
            $admins[] = $user;
        }
        $this->propertyAccessor->setValue($group, 'admins', $admins);

        // 群成员列表
        $members = [];
        foreach ($response['group_chat']['member_list'] as $item) {
            $member = new GroupMember();
            $member->setGroupChat($group);
            $member->setUserId($item['userid']);
            $member->setType($item['type']);
            $member->setJoinTime(Carbon::createFromTimestamp($item['join_time'], date_default_timezone_get()));
            $member->setJoinScene($item['join_scene']);
            $member->setInvitorUserId($item['invitor']['userid']);
            $member->setGroupNickname($item['group_nickname']);
            $member->setName($item['name']);
            $members[] = $member;
        }
        $this->propertyAccessor->setValue($group, 'members', $members);

        $this->entityManager->persist($group);
        $this->entityManager->flush();
    }
}
