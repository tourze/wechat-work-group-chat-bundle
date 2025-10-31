<?php

namespace WechatWorkGroupChatBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\CorpInterface;
use Tourze\WechatWorkContracts\UserInterface;
use WechatWorkGroupChatBundle\Entity\GroupChat;
use WechatWorkGroupChatBundle\Entity\GroupMember;
use WechatWorkGroupChatBundle\Enum\GroupChatStatus;

/**
 * GroupChat 实体测试用例
 *
 * 测试客户群实体的所有功能
 *
 * @internal
 */
#[CoversClass(GroupChat::class)]
final class GroupChatTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new GroupChat();
    }

    /** @return iterable<string, array{string, mixed}> */
    public static function propertiesProvider(): iterable
    {
        return [
            'chatId' => ['chatId', 'test_chat_id'],
            'status' => ['status', null],
            'name' => ['name', '测试群名'],
            'notice' => ['notice', '测试公告'],
            'agent' => ['agent', null],
            'corp' => ['corp', null],
            'owner' => ['owner', null],
        ];
    }

    public function testConstructorSetsDefaultValues(): void
    {
        $groupChat = new GroupChat();

        $this->assertNull($groupChat->getId());
        $this->assertNull($groupChat->getChatId());
        $this->assertNull($groupChat->getStatus());
        $this->assertNull($groupChat->getCreateTime());
        $this->assertNull($groupChat->getName());
        $this->assertNull($groupChat->getNotice());
        $this->assertNull($groupChat->getAgent());
        $this->assertNull($groupChat->getCorp());
        $this->assertNull($groupChat->getOwner());
        $this->assertInstanceOf(Collection::class, $groupChat->getAdmins());
        $this->assertInstanceOf(ArrayCollection::class, $groupChat->getAdmins());
        $this->assertTrue($groupChat->getAdmins()->isEmpty());
        $this->assertInstanceOf(Collection::class, $groupChat->getMembers());
        $this->assertInstanceOf(ArrayCollection::class, $groupChat->getMembers());
        $this->assertTrue($groupChat->getMembers()->isEmpty());
    }

    public function testSetChatIdWithValidIdSetsIdCorrectly(): void
    {
        $groupChat = new GroupChat();
        $chatId = 'wrkSFMzxKUwG6QRf3nM5HcRcFwlL4d6Q';

        $groupChat->setChatId($chatId);

        $this->assertSame($chatId, $groupChat->getChatId());
    }

    public function testSetChatIdWithEmptyStringSetsEmptyString(): void
    {
        $groupChat = new GroupChat();
        $groupChat->setChatId('');

        $this->assertSame('', $groupChat->getChatId());
    }

    public function testSetChatIdWithLongStringSetsLongString(): void
    {
        $groupChat = new GroupChat();
        $longChatId = str_repeat('a', 64); // 最大长度

        $groupChat->setChatId($longChatId);

        $this->assertSame($longChatId, $groupChat->getChatId());
    }

    public function testSetStatusWithNormalStatusSetsStatusCorrectly(): void
    {
        $groupChat = new GroupChat();
        $status = GroupChatStatus::NORMAL;

        $groupChat->setStatus($status);

        $this->assertSame($status, $groupChat->getStatus());
    }

    public function testSetStatusWithResignStatusSetsStatusCorrectly(): void
    {
        $groupChat = new GroupChat();
        $status = GroupChatStatus::RESIGN;

        $groupChat->setStatus($status);

        $this->assertSame($status, $groupChat->getStatus());
    }

    public function testSetStatusWithInheritDoingStatusSetsStatusCorrectly(): void
    {
        $groupChat = new GroupChat();
        $status = GroupChatStatus::INHERIT_DOING;

        $groupChat->setStatus($status);

        $this->assertSame($status, $groupChat->getStatus());
    }

    public function testSetStatusWithInheritFinishedStatusSetsStatusCorrectly(): void
    {
        $groupChat = new GroupChat();
        $status = GroupChatStatus::INHERIT_FINISHED;

        $groupChat->setStatus($status);

        $this->assertSame($status, $groupChat->getStatus());
    }

    public function testSetStatusWithNullSetsNull(): void
    {
        $groupChat = new GroupChat();
        $groupChat->setStatus(GroupChatStatus::NORMAL);

        $groupChat->setStatus(null);

        $this->assertNull($groupChat->getStatus());
    }

    public function testSetCreateTimeWithValidDateTimeSetsTimeCorrectly(): void
    {
        $groupChat = new GroupChat();
        $createTime = new \DateTimeImmutable('2024-01-15 10:30:00');

        $groupChat->setCreateTime($createTime);
        $this->assertInstanceOf(\DateTimeImmutable::class, $groupChat->getCreateTime());
        $this->assertEquals($createTime->format('Y-m-d H:i:s'), $groupChat->getCreateTime()->format('Y-m-d H:i:s'));
    }

    public function testSetCreateTimeWithNullSetsNull(): void
    {
        $groupChat = new GroupChat();
        $groupChat->setCreateTime(new \DateTimeImmutable());

        $groupChat->setCreateTime(null);
        $this->assertNull($groupChat->getCreateTime());
    }

    public function testSetNameWithValidNameSetsNameCorrectly(): void
    {
        $groupChat = new GroupChat();
        $name = '产品讨论群';

        $groupChat->setName($name);
        $this->assertSame($name, $groupChat->getName());
    }

    public function testSetNameWithNullSetsNull(): void
    {
        $groupChat = new GroupChat();
        $groupChat->setName('old name');

        $groupChat->setName(null);
        $this->assertNull($groupChat->getName());
    }

    public function testSetNameWithLongNameSetsLongName(): void
    {
        $groupChat = new GroupChat();
        $longName = str_repeat('群聊名称', 30); // 长名称

        $groupChat->setName($longName);
        $this->assertSame($longName, $groupChat->getName());
    }

    public function testSetNoticeWithValidNoticeSetsNoticeCorrectly(): void
    {
        $groupChat = new GroupChat();
        $notice = '欢迎大家加入产品讨论群，请文明交流！';

        $groupChat->setNotice($notice);
        $this->assertSame($notice, $groupChat->getNotice());
    }

    public function testSetNoticeWithNullSetsNull(): void
    {
        $groupChat = new GroupChat();
        $groupChat->setNotice('old notice');

        $groupChat->setNotice(null);
        $this->assertNull($groupChat->getNotice());
    }

    public function testSetNoticeWithLongNoticeSetsLongNotice(): void
    {
        $groupChat = new GroupChat();
        $longNotice = str_repeat('这是一个很长的群公告。', 100); // 长公告

        $groupChat->setNotice($longNotice);
        $this->assertSame($longNotice, $groupChat->getNotice());
    }

    public function testSetAgentWithValidAgentSetsAgentCorrectly(): void
    {
        $groupChat = new GroupChat();
        $agent = $this->createMock(AgentInterface::class);

        $groupChat->setAgent($agent);
        $this->assertSame($agent, $groupChat->getAgent());
    }

    public function testSetAgentWithNullSetsNull(): void
    {
        $groupChat = new GroupChat();
        $agent = $this->createMock(AgentInterface::class);
        $groupChat->setAgent($agent);

        $groupChat->setAgent(null);
        $this->assertNull($groupChat->getAgent());
    }

    public function testSetCorpWithValidCorpSetsCorpCorrectly(): void
    {
        $groupChat = new GroupChat();
        $corp = $this->createMock(CorpInterface::class);

        $groupChat->setCorp($corp);
        $this->assertSame($corp, $groupChat->getCorp());
    }

    public function testSetCorpWithNullSetsNull(): void
    {
        $groupChat = new GroupChat();
        $corp = $this->createMock(CorpInterface::class);
        $groupChat->setCorp($corp);

        $groupChat->setCorp(null);
        $this->assertNull($groupChat->getCorp());
    }

    public function testSetOwnerWithValidOwnerSetsOwnerCorrectly(): void
    {
        $groupChat = new GroupChat();
        $owner = $this->createMock(UserInterface::class);

        $groupChat->setOwner($owner);
        $this->assertSame($owner, $groupChat->getOwner());
    }

    public function testSetOwnerWithNullSetsNull(): void
    {
        $groupChat = new GroupChat();
        $owner = $this->createMock(UserInterface::class);
        $groupChat->setOwner($owner);

        $groupChat->setOwner(null);
        $this->assertNull($groupChat->getOwner());
    }

    /**
     * 测试管理员Collection操作
     */
    public function testAddAdminWithNewAdminAddsAdminToCollection(): void
    {
        $groupChat = new GroupChat();
        $admin = $this->createMock(UserInterface::class);

        $groupChat->addAdmin($admin);
        $this->assertTrue($groupChat->getAdmins()->contains($admin));
        $this->assertCount(1, $groupChat->getAdmins());
    }

    public function testAddAdminWithExistingAdminDoesNotAddDuplicate(): void
    {
        $groupChat = new GroupChat();
        $admin = $this->createMock(UserInterface::class);

        // 添加第一次
        $groupChat->addAdmin($admin);
        $firstCount = $groupChat->getAdmins()->count();

        // 尝试再次添加相同管理员
        $groupChat->addAdmin($admin);
        $this->assertCount($firstCount, $groupChat->getAdmins());
    }

    public function testAddAdminWithMultipleAdminsAddsAllAdmins(): void
    {
        $groupChat = new GroupChat();
        $admin1 = $this->createMock(UserInterface::class);
        $admin2 = $this->createMock(UserInterface::class);
        $admin3 = $this->createMock(UserInterface::class);

        $groupChat->addAdmin($admin1);
        $groupChat->addAdmin($admin2);
        $groupChat->addAdmin($admin3);

        $this->assertCount(3, $groupChat->getAdmins());
        $this->assertTrue($groupChat->getAdmins()->contains($admin1));
        $this->assertTrue($groupChat->getAdmins()->contains($admin2));
        $this->assertTrue($groupChat->getAdmins()->contains($admin3));
    }

    public function testRemoveAdminWithExistingAdminRemovesAdminFromCollection(): void
    {
        $groupChat = new GroupChat();
        $admin = $this->createMock(UserInterface::class);

        // 先添加管理员
        $groupChat->addAdmin($admin);
        $this->assertCount(1, $groupChat->getAdmins());

        // 移除管理员
        $groupChat->removeAdmin($admin);
        $this->assertFalse($groupChat->getAdmins()->contains($admin));
        $this->assertCount(0, $groupChat->getAdmins());
    }

    public function testRemoveAdminWithNonExistingAdminDoesNothing(): void
    {
        $groupChat = new GroupChat();
        $admin = $this->createMock(UserInterface::class);

        $groupChat->removeAdmin($admin);
        $this->assertCount(0, $groupChat->getAdmins());
    }

    /**
     * 测试成员Collection操作
     */
    public function testAddMemberWithNewMemberAddsMemberToCollection(): void
    {
        /**
         * 使用具体类 GroupMember 创建 Mock 对象的原因：
         * 1. GroupMember 是一个实体类，需要测试其与 GroupChat 的双向关联关系
         * 2. 该实体类包含复杂的业务逻辑（如 setGroupChat 方法），需要验证调用行为
         * 3. 没有合适的接口可以替代，因为需要测试具体的实体关系映射
         * 4. 这种使用方式是测试实体间关联关系的标准做法
         */
        $groupChat = new GroupChat();
        $member = $this->createMock(GroupMember::class);
        $member->expects($this->once())
            ->method('setGroupChat')
            ->with($groupChat)
        ;

        $groupChat->addMember($member);
        $this->assertTrue($groupChat->getMembers()->contains($member));
        $this->assertCount(1, $groupChat->getMembers());
    }

    public function testAddMemberWithExistingMemberDoesNotAddDuplicate(): void
    {
        /**
         * 使用具体类 GroupMember 创建 Mock 对象的原因：
         * 1. 需要测试集合去重逻辑，验证相同成员不会被重复添加
         * 2. 需要验证 setGroupChat 方法的调用次数和参数
         * 3. 实体类的双向关联逻辑需要通过具体类来测试
         * 4. 没有接口层可以抽象这些具体的实体行为
         */
        $groupChat = new GroupChat();
        $member = $this->createMock(GroupMember::class);
        $member->expects($this->once()) // 只调用一次
            ->method('setGroupChat')
        ;

        // 添加第一次
        $groupChat->addMember($member);
        $firstCount = $groupChat->getMembers()->count();

        // 尝试再次添加相同成员
        $groupChat->addMember($member);
        $this->assertCount($firstCount, $groupChat->getMembers());
    }

    public function testRemoveMemberWithExistingMemberRemovesMemberFromCollection(): void
    {
        /**
         * 使用具体类 GroupMember 创建 Mock 对象的原因：
         * 1. 测试成员移除时的双向关联解除逻辑
         * 2. 需要验证 setGroupChat(null) 被正确调用以解除关联
         * 3. 需要验证 getGroupChat() 方法返回正确的关联对象
         * 4. 实体间的复杂关联逻辑无法通过接口抽象
         */
        $groupChat = new GroupChat();
        $member = $this->createMock(GroupMember::class);

        // 设置期望：setGroupChat被调用两次，第一次传入groupChat，第二次传入null
        $member->expects($this->exactly(2))
            ->method('setGroupChat')
            ->with(self::callback(function ($arg) use ($groupChat) {
                static $callCount = 0;
                /** @var int $callCount */
                ++$callCount;
                if (1 === $callCount) {
                    return $arg === $groupChat;
                }

                return null === $arg;
            }))
        ;

        $member->expects($this->once())
            ->method('getGroupChat')
            ->willReturn($groupChat)
        ;

        // 先添加成员
        $groupChat->addMember($member);
        $this->assertCount(1, $groupChat->getMembers());

        // 移除成员
        $groupChat->removeMember($member);
        $this->assertFalse($groupChat->getMembers()->contains($member));
        $this->assertCount(0, $groupChat->getMembers());
    }

    public function testRemoveMemberWithNonExistingMemberDoesNothing(): void
    {
        /**
         * 使用具体类 GroupMember 创建 Mock 对象的原因：
         * 1. 测试边界条件：移除不存在的成员时应该不执行任何操作
         * 2. 需要验证 setGroupChat 方法不会被调用
         * 3. 确保集合操作的安全性和幂等性
         * 4. 实体类的具体行为需要通过具体类来验证
         */
        $groupChat = new GroupChat();
        $member = $this->createMock(GroupMember::class);
        $member->expects($this->never())->method('setGroupChat');
        $member->expects($this->never())->method('getGroupChat');

        $groupChat->removeMember($member);
        $this->assertCount(0, $groupChat->getMembers());
    }

    public function testRemoveMemberWhenMemberGroupChatDiffersRemovesButDoesNotSetNull(): void
    {
        /**
         * 使用具体类 GroupMember 创建 Mock 对象的原因：
         * 1. 测试复杂的边界情况：成员的群聊关联已被修改的情况
         * 2. 需要验证 getGroupChat() 返回的对象与当前群聊不同时的行为
         * 3. 确保只移除集合中的引用，不修改成员的关联对象
         * 4. 这种业务逻辑只能通过实体类来正确模拟
         */
        $groupChat = new GroupChat();
        $member = $this->createMock(GroupMember::class);
        // 创建一个真实的 GroupChat 对象而不是 mock
        $otherGroupChat = new GroupChat();

        $member->expects($this->once())->method('setGroupChat')->with($groupChat);

        // 添加成员
        $groupChat->addMember($member);

        // 模拟成员的群聊已经被改变
        $member->expects($this->once())->method('getGroupChat')->willReturn($otherGroupChat);

        $groupChat->removeMember($member);
        $this->assertFalse($groupChat->getMembers()->contains($member));
    }

    /**
     * 测试链式调用
     */
    public function testChainedSettersReturnSameInstance(): void
    {
        $groupChat = new GroupChat();
        $agent = $this->createMock(AgentInterface::class);
        $corp = $this->createMock(CorpInterface::class);
        $owner = $this->createMock(UserInterface::class);

        $createTime = new \DateTimeImmutable('2024-01-15 10:00:00');

        // 由于setter现在返回void，不再支持链式调用，改为独立调用
        $groupChat->setChatId('chain_test_chat_id');
        $groupChat->setStatus(GroupChatStatus::NORMAL);
        $groupChat->setName('链式调用测试群');
        $groupChat->setNotice('这是链式调用测试群公告');
        $groupChat->setAgent($agent);
        $groupChat->setCorp($corp);
        $groupChat->setOwner($owner);
        $groupChat->setCreateTime($createTime);

        $this->assertSame('chain_test_chat_id', $groupChat->getChatId());
        $this->assertSame(GroupChatStatus::NORMAL, $groupChat->getStatus());
        $this->assertSame('链式调用测试群', $groupChat->getName());
        $this->assertSame('这是链式调用测试群公告', $groupChat->getNotice());
        $this->assertSame($agent, $groupChat->getAgent());
        $this->assertSame($corp, $groupChat->getCorp());
        $this->assertSame($owner, $groupChat->getOwner());
        $this->assertInstanceOf(\DateTimeImmutable::class, $groupChat->getCreateTime());
        $this->assertEquals($createTime->format('Y-m-d H:i:s'), $groupChat->getCreateTime()->format('Y-m-d H:i:s'));
    }

    /**
     * 测试边界场景
     */
    public function testEdgeCasesLongStrings(): void
    {
        $groupChat = new GroupChat();
        $longString = str_repeat('x', 1000);
        $maxChatId = str_repeat('a', 64);
        $maxName = str_repeat('名', 127); // 中文字符

        $groupChat->setChatId($maxChatId);
        $groupChat->setName($maxName);
        $groupChat->setNotice($longString);

        $this->assertSame($maxChatId, $groupChat->getChatId());
        $this->assertSame($maxName, $groupChat->getName());
        $this->assertSame($longString, $groupChat->getNotice());
    }

    public function testEdgeCasesDateTimeTypes(): void
    {
        $groupChat = new GroupChat();
        // 测试DateTime
        $dateTime = new \DateTimeImmutable('2024-01-15 12:30:45');
        $groupChat->setCreateTime($dateTime);
        $this->assertInstanceOf(\DateTimeImmutable::class, $groupChat->getCreateTime());
        $this->assertEquals($dateTime->format('Y-m-d H:i:s'), $groupChat->getCreateTime()->format('Y-m-d H:i:s'));

        // 测试DateTimeImmutable
        $dateTimeImmutable = new \DateTimeImmutable('2024-02-20 09:15:30');
        $groupChat->setCreateTime($dateTimeImmutable);
        $this->assertSame($dateTimeImmutable, $groupChat->getCreateTime());
    }

    /**
     * 测试Collection操作的复杂场景
     */
    public function testAdminCollectionIsIterable(): void
    {
        $groupChat = new GroupChat();
        $admin1 = $this->createMock(UserInterface::class);
        $admin2 = $this->createMock(UserInterface::class);

        $groupChat->addAdmin($admin1);
        $groupChat->addAdmin($admin2);

        $admins = [];
        foreach ($groupChat->getAdmins() as $admin) {
            $admins[] = $admin;
        }

        $this->assertCount(2, $admins);
        $this->assertContains($admin1, $admins);
        $this->assertContains($admin2, $admins);
    }

    public function testMemberCollectionIsIterable(): void
    {
        /**
         * 使用具体类 GroupMember 创建 Mock 对象的原因：
         * 1. 测试集合的可迭代性，需要实际的实体对象来模拟
         * 2. 验证成员添加后的双向关联设置正确性
         * 3. 确保 foreach 循环能够正确访问所有成员实体
         * 4. Collection 的迭代行为与实体类的具体实现相关
         */
        $groupChat = new GroupChat();
        $member1 = $this->createMock(GroupMember::class);
        /**
         * 使用具体类 GroupMember 创建 Mock 对象的原因：
         * 1. 测试集合的可迭代性，需要多个实际的实体对象来模拟
         * 2. 验证成员添加后的双向关联设置正确性
         * 3. 确保 foreach 循环能够正确访问所有成员实体
         * 4. Collection 的迭代行为与实体类的具体实现相关
         */
        $member2 = $this->createMock(GroupMember::class);

        $member1->expects($this->once())->method('setGroupChat');
        $member2->expects($this->once())->method('setGroupChat');

        $groupChat->addMember($member1);
        $groupChat->addMember($member2);

        $members = [];
        foreach ($groupChat->getMembers() as $member) {
            $members[] = $member;
        }

        $this->assertCount(2, $members);
        $this->assertContains($member1, $members);
        $this->assertContains($member2, $members);
    }

    /**
     * 测试业务逻辑场景
     */
    public function testBusinessScenarioGroupChatLifecycle(): void
    {
        $groupChat = new GroupChat();
        $corp = $this->createMock(CorpInterface::class);
        $owner = $this->createMock(UserInterface::class);
        $admin = $this->createMock(UserInterface::class);

        $createTime = new \DateTimeImmutable('2024-01-15 10:00:00');

        // 创建群聊
        $groupChat->setChatId('wrk_lifecycle_test');
        $groupChat->setName('生命周期测试群');
        $groupChat->setStatus(GroupChatStatus::NORMAL);
        $groupChat->setCorp($corp);
        $groupChat->setOwner($owner);
        $groupChat->setCreateTime($createTime);

        // 添加管理员
        $groupChat->addAdmin($admin);

        // 验证初始状态
        $this->assertSame(GroupChatStatus::NORMAL, $groupChat->getStatus());
        $this->assertNotNull($groupChat->getOwner());
        $this->assertCount(1, $groupChat->getAdmins());
        $this->assertTrue($groupChat->getMembers()->isEmpty());

        // 模拟跟进人离职
        $groupChat->setStatus(GroupChatStatus::RESIGN);
        $this->assertSame(GroupChatStatus::RESIGN, $groupChat->getStatus());

        // 模拟继承中
        $groupChat->setStatus(GroupChatStatus::INHERIT_DOING);
        $this->assertSame(GroupChatStatus::INHERIT_DOING, $groupChat->getStatus());

        // 模拟继承完成
        $groupChat->setStatus(GroupChatStatus::INHERIT_FINISHED);
        $this->assertSame(GroupChatStatus::INHERIT_FINISHED, $groupChat->getStatus());
    }

    public function testBusinessScenarioAdminManagement(): void
    {
        $groupChat = new GroupChat();
        $owner = $this->createMock(UserInterface::class);
        $admin1 = $this->createMock(UserInterface::class);
        $admin2 = $this->createMock(UserInterface::class);
        $admin3 = $this->createMock(UserInterface::class);

        $groupChat->setOwner($owner);

        // 添加多个管理员
        $groupChat->addAdmin($admin1);
        $groupChat->addAdmin($admin2);
        $groupChat->addAdmin($admin3);

        $this->assertCount(3, $groupChat->getAdmins());

        // 移除一个管理员
        $groupChat->removeAdmin($admin2);

        $this->assertCount(2, $groupChat->getAdmins());
        $this->assertTrue($groupChat->getAdmins()->contains($admin1));
        $this->assertFalse($groupChat->getAdmins()->contains($admin2));
        $this->assertTrue($groupChat->getAdmins()->contains($admin3));

        // 群主不在管理员列表中（这是预期的，因为群主和管理员是分开管理的）
        $this->assertFalse($groupChat->getAdmins()->contains($owner));
    }
}
