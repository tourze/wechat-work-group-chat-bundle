<?php

namespace WechatWorkGroupChatBundle\MessageHandler;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Tourze\WechatWorkContracts\CorpInterface;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Service\WorkServiceInterface;
use WechatWorkGroupChatBundle\Entity\GroupChat;
use WechatWorkGroupChatBundle\Entity\GroupMember;
use WechatWorkGroupChatBundle\Message\SyncGroupChatDetailMessage;
use WechatWorkGroupChatBundle\Repository\GroupChatRepository;
use WechatWorkGroupChatBundle\Request\GetGroupChatDetailRequest;

/**
 * @see https://developer.work.weixin.qq.com/document/path/92122
 */
#[AsMessageHandler]
readonly class SyncGroupChatDetailHandler
{
    public function __construct(
        private GroupChatRepository $groupChatRepository,
        private WorkServiceInterface $workService,
        private UserLoaderInterface $userLoader,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(SyncGroupChatDetailMessage $message): void
    {
        $group = $this->findGroupChat($message->getChatId());
        $groupChatData = $this->fetchGroupChatData($group);

        $this->updateGroupBasicInfo($group, $groupChatData);
        $this->updateGroupOwner($group, $groupChatData);
        $this->updateGroupAdmins($group, $groupChatData);
        $this->updateGroupMembers($group, $groupChatData);

        $this->entityManager->persist($group);
        $this->entityManager->flush();
    }

    private function findGroupChat(string $chatId): GroupChat
    {
        $group = $this->groupChatRepository->findOneBy(['chatId' => $chatId]);
        if (!$group instanceof GroupChat) {
            throw new UnrecoverableMessageHandlingException('数据库中找不到客户群信息');
        }

        return $group;
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchGroupChatData(GroupChat $group): array
    {
        $request = new GetGroupChatDetailRequest();
        $request->setAgent($group->getAgent());
        $chatId = $group->getChatId();
        if (null === $chatId) {
            throw new UnrecoverableMessageHandlingException('客户群ID为空');
        }
        $request->setChatId($chatId);
        $request->setNeedName(true);
        $response = $this->workService->request($request);

        if (!is_array($response) || !isset($response['group_chat']) || !is_array($response['group_chat'])) {
            throw new UnrecoverableMessageHandlingException('接口中找不到客户群信息');
        }

        /** @var array<string, mixed> */
        return $response['group_chat'];
    }

    /**
     * @param array<string, mixed> $groupChatData
     */
    private function updateGroupBasicInfo(GroupChat $group, array $groupChatData): void
    {
        if (isset($groupChatData['name']) && is_string($groupChatData['name'])) {
            $group->setName($groupChatData['name']);
        }

        if (isset($groupChatData['notice']) && is_string($groupChatData['notice'])) {
            $group->setNotice($groupChatData['notice']);
        }

        if (isset($groupChatData['create_time']) && is_numeric($groupChatData['create_time'])) {
            $group->setCreateTime(CarbonImmutable::createFromTimestamp((int) $groupChatData['create_time'], date_default_timezone_get()));
        }
    }

    /**
     * @param array<string, mixed> $groupChatData
     */
    private function updateGroupOwner(GroupChat $group, array $groupChatData): void
    {
        if (!isset($groupChatData['owner']) || !is_string($groupChatData['owner'])) {
            return;
        }

        $corp = $group->getCorp();
        if (null === $corp) {
            return;
        }

        $user = $this->userLoader->loadUserByUserIdAndCorp($groupChatData['owner'], $corp);
        if (null !== $user) {
            $group->setOwner($user);
        }
    }

    /**
     * @param array<string, mixed> $groupChatData
     */
    private function updateGroupAdmins(GroupChat $group, array $groupChatData): void
    {
        $this->clearGroupAdmins($group);

        if (!isset($groupChatData['admin_list']) || !is_array($groupChatData['admin_list'])) {
            return;
        }

        $corp = $group->getCorp();
        if (null === $corp) {
            return;
        }

        foreach ($groupChatData['admin_list'] as $item) {
            $this->addAdminFromItem($group, $item, $corp);
        }
    }

    private function clearGroupAdmins(GroupChat $group): void
    {
        foreach ($group->getAdmins() as $admin) {
            $group->removeAdmin($admin);
        }
    }

    /**
     * @param mixed $item
     */
    private function addAdminFromItem(GroupChat $group, $item, CorpInterface $corp): void
    {
        if (!is_array($item) || !isset($item['userid']) || !is_string($item['userid'])) {
            return;
        }

        $user = $this->userLoader->loadUserByUserIdAndCorp($item['userid'], $corp);
        if (null !== $user) {
            $group->addAdmin($user);
        }
    }

    /**
     * @param array<string, mixed> $groupChatData
     */
    private function updateGroupMembers(GroupChat $group, array $groupChatData): void
    {
        $this->clearGroupMembers($group);

        if (!isset($groupChatData['member_list']) || !is_array($groupChatData['member_list'])) {
            return;
        }

        foreach ($groupChatData['member_list'] as $item) {
            $this->addMemberFromItem($group, $item);
        }
    }

    private function clearGroupMembers(GroupChat $group): void
    {
        foreach ($group->getMembers() as $member) {
            $group->removeMember($member);
        }
    }

    /**
     * @param mixed $item
     */
    private function addMemberFromItem(GroupChat $group, $item): void
    {
        if (!is_array($item)) {
            return;
        }

        $member = new GroupMember();
        $member->setGroupChat($group);

        /** @var array<string, mixed> $item */
        $this->setMemberBasicInfo($member, $item);
        $this->setMemberJoinInfo($member, $item);
        $this->setMemberNames($member, $item);

        $group->addMember($member);
    }

    /**
     * @param array<string, mixed> $item
     */
    private function setMemberBasicInfo(GroupMember $member, array $item): void
    {
        if (isset($item['userid']) && is_string($item['userid'])) {
            $member->setUserId($item['userid']);
        }

        if (isset($item['type']) && is_int($item['type'])) {
            $member->setType($item['type']);
        }
    }

    /**
     * @param array<string, mixed> $item
     */
    private function setMemberJoinInfo(GroupMember $member, array $item): void
    {
        if (isset($item['join_time']) && is_numeric($item['join_time'])) {
            $member->setJoinTime(CarbonImmutable::createFromTimestamp((int) $item['join_time'], date_default_timezone_get()));
        }

        if (isset($item['join_scene']) && is_int($item['join_scene'])) {
            $member->setJoinScene($item['join_scene']);
        }

        if (isset($item['invitor']) && is_array($item['invitor']) && isset($item['invitor']['userid']) && is_string($item['invitor']['userid'])) {
            $member->setInvitorUserId($item['invitor']['userid']);
        }
    }

    /**
     * @param array<string, mixed> $item
     */
    private function setMemberNames(GroupMember $member, array $item): void
    {
        if (isset($item['group_nickname']) && is_string($item['group_nickname'])) {
            $member->setGroupNickname($item['group_nickname']);
        }

        if (isset($item['name']) && is_string($item['name'])) {
            $member->setName($item['name']);
        }
    }
}
