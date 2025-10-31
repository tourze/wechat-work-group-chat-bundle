<?php

namespace WechatWorkGroupChatBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatWorkGroupChatBundle\Entity\GroupChat;
use WechatWorkGroupChatBundle\Repository\GroupChatRepository;

/**
 * @template-extends AbstractRepositoryTestCase<GroupChat>
 * @internal
 */
#[CoversClass(GroupChatRepository::class)]
#[RunTestsInSeparateProcesses]
final class GroupChatRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function createNewEntity(): object
    {
        $groupChat = new GroupChat();
        $groupChat->setChatId('test_chat_' . uniqid());

        return $groupChat;
    }

    protected function getRepository(): GroupChatRepository
    {
        return self::getService(GroupChatRepository::class);
    }

    public function testSaveAndFindGroupChat(): void
    {
        $repository = $this->getRepository();
        $groupChat = new GroupChat();
        $groupChat->setChatId('custom_save_test_' . uniqid());

        $repository->save($groupChat);

        $found = $repository->findOneBy(['chatId' => $groupChat->getChatId()]);
        $this->assertNotNull($found);
        $this->assertEquals($groupChat->getChatId(), $found->getChatId());
    }

    public function testRemoveGroupChat(): void
    {
        $repository = $this->getRepository();
        $groupChat = new GroupChat();
        $groupChat->setChatId('custom_remove_test_' . uniqid());
        $repository->save($groupChat);

        $repository->remove($groupChat);

        $found = $repository->findOneBy(['chatId' => $groupChat->getChatId()]);
        $this->assertNull($found);
    }
}
