<?php

namespace WechatWorkGroupChatBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatWorkGroupChatBundle\Entity\GroupChat;
use WechatWorkGroupChatBundle\Entity\GroupMember;
use WechatWorkGroupChatBundle\Repository\GroupMemberRepository;

/**
 * @template-extends AbstractRepositoryTestCase<GroupMember>
 * @internal
 */
#[CoversClass(GroupMemberRepository::class)]
#[RunTestsInSeparateProcesses]
final class GroupMemberRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function createNewEntity(): object
    {
        $groupChat = new GroupChat();
        $groupChat->setChatId('test_group_' . uniqid());
        $em = self::getEntityManager();
        $em->persist($groupChat);
        $em->flush();

        $groupMember = new GroupMember();
        $groupMember->setUserId('test_user_' . uniqid());
        $groupMember->setGroupChat($groupChat);

        return $groupMember;
    }

    protected function getRepository(): GroupMemberRepository
    {
        return self::getService(GroupMemberRepository::class);
    }

    public function testSaveAndFindGroupMember(): void
    {
        $repository = $this->getRepository();

        $groupChat = new GroupChat();
        $groupChat->setChatId('custom_save_group_' . uniqid());
        $em = self::getEntityManager();
        $em->persist($groupChat);
        $em->flush();

        $groupMember = new GroupMember();
        $groupMember->setUserId('custom_save_test_user_' . uniqid());
        $groupMember->setGroupChat($groupChat);

        $repository->save($groupMember);

        $found = $repository->findOneBy(['userId' => $groupMember->getUserId()]);
        $this->assertNotNull($found);
        $this->assertEquals($groupMember->getUserId(), $found->getUserId());
    }

    public function testRemoveGroupMember(): void
    {
        $repository = $this->getRepository();

        $groupChat = new GroupChat();
        $groupChat->setChatId('custom_remove_group_' . uniqid());
        $em = self::getEntityManager();
        $em->persist($groupChat);
        $em->flush();

        $groupMember = new GroupMember();
        $groupMember->setUserId('custom_remove_test_user_' . uniqid());
        $groupMember->setGroupChat($groupChat);
        $repository->save($groupMember);

        $repository->remove($groupMember);

        $found = $repository->findOneBy(['userId' => $groupMember->getUserId()]);
        $this->assertNull($found);
    }
}
