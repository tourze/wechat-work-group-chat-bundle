<?php

namespace WechatWorkGroupChatBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
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
 */
class GroupChatTest extends TestCase
{
    private GroupChat $groupChat;

    protected function setUp(): void
    {
        $this->groupChat = new GroupChat();
    }

    public function test_constructor_setsDefaultValues(): void
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

    public function test_setChatId_withValidId_setsIdCorrectly(): void
    {
        $chatId = 'wrkSFMzxKUwG6QRf3nM5HcRcFwlL4d6Q';
        
        $result = $this->groupChat->setChatId($chatId);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertSame($chatId, $this->groupChat->getChatId());
    }

    public function test_setChatId_withEmptyString_setsEmptyString(): void
    {
        $result = $this->groupChat->setChatId('');
        
        $this->assertSame($this->groupChat, $result);
        $this->assertSame('', $this->groupChat->getChatId());
    }

    public function test_setChatId_withLongString_setsLongString(): void
    {
        $longChatId = str_repeat('a', 64); // 最大长度
        
        $result = $this->groupChat->setChatId($longChatId);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertSame($longChatId, $this->groupChat->getChatId());
    }

    public function test_setStatus_withNormalStatus_setsStatusCorrectly(): void
    {
        $status = GroupChatStatus::NORMAL;
        
        $result = $this->groupChat->setStatus($status);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertSame($status, $this->groupChat->getStatus());
    }

    public function test_setStatus_withResignStatus_setsStatusCorrectly(): void
    {
        $status = GroupChatStatus::RESIGN;
        
        $result = $this->groupChat->setStatus($status);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertSame($status, $this->groupChat->getStatus());
    }

    public function test_setStatus_withInheritDoingStatus_setsStatusCorrectly(): void
    {
        $status = GroupChatStatus::INHERIT_DOING;
        
        $result = $this->groupChat->setStatus($status);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertSame($status, $this->groupChat->getStatus());
    }

    public function test_setStatus_withInheritFinishedStatus_setsStatusCorrectly(): void
    {
        $status = GroupChatStatus::INHERIT_FINISHED;
        
        $result = $this->groupChat->setStatus($status);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertSame($status, $this->groupChat->getStatus());
    }

    public function test_setStatus_withNull_setsNull(): void
    {
        $this->groupChat->setStatus(GroupChatStatus::NORMAL);
        
        $result = $this->groupChat->setStatus(null);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertNull($this->groupChat->getStatus());
    }

    public function test_setCreateTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $createTime = new \DateTime('2024-01-15 10:30:00');
        
        $result = $this->groupChat->setCreateTime($createTime);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertSame($createTime, $this->groupChat->getCreateTime());
    }

    public function test_setCreateTime_withNull_setsNull(): void
    {
        $this->groupChat->setCreateTime(new \DateTime());
        
        $result = $this->groupChat->setCreateTime(null);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertNull($this->groupChat->getCreateTime());
    }

    public function test_setName_withValidName_setsNameCorrectly(): void
    {
        $name = '产品讨论群';
        
        $result = $this->groupChat->setName($name);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertSame($name, $this->groupChat->getName());
    }

    public function test_setName_withNull_setsNull(): void
    {
        $this->groupChat->setName('old name');
        
        $result = $this->groupChat->setName(null);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertNull($this->groupChat->getName());
    }

    public function test_setName_withLongName_setsLongName(): void
    {
        $longName = str_repeat('群聊名称', 30); // 长名称
        
        $result = $this->groupChat->setName($longName);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertSame($longName, $this->groupChat->getName());
    }

    public function test_setNotice_withValidNotice_setsNoticeCorrectly(): void
    {
        $notice = '欢迎大家加入产品讨论群，请文明交流！';
        
        $result = $this->groupChat->setNotice($notice);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertSame($notice, $this->groupChat->getNotice());
    }

    public function test_setNotice_withNull_setsNull(): void
    {
        $this->groupChat->setNotice('old notice');
        
        $result = $this->groupChat->setNotice(null);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertNull($this->groupChat->getNotice());
    }

    public function test_setNotice_withLongNotice_setsLongNotice(): void
    {
        $longNotice = str_repeat('这是一个很长的群公告。', 100); // 长公告
        
        $result = $this->groupChat->setNotice($longNotice);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertSame($longNotice, $this->groupChat->getNotice());
    }

    public function test_setAgent_withValidAgent_setsAgentCorrectly(): void
    {
        /** @var AgentInterface&MockObject $agent */
        $agent = $this->createMock(AgentInterface::class);
        
        $result = $this->groupChat->setAgent($agent);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertSame($agent, $this->groupChat->getAgent());
    }

    public function test_setAgent_withNull_setsNull(): void
    {
        /** @var AgentInterface&MockObject $agent */
        $agent = $this->createMock(AgentInterface::class);
        $this->groupChat->setAgent($agent);
        
        $result = $this->groupChat->setAgent(null);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertNull($this->groupChat->getAgent());
    }

    public function test_setCorp_withValidCorp_setsCorpCorrectly(): void
    {
        /** @var CorpInterface&MockObject $corp */
        $corp = $this->createMock(CorpInterface::class);
        
        $result = $this->groupChat->setCorp($corp);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertSame($corp, $this->groupChat->getCorp());
    }

    public function test_setCorp_withNull_setsNull(): void
    {
        /** @var CorpInterface&MockObject $corp */
        $corp = $this->createMock(CorpInterface::class);
        $this->groupChat->setCorp($corp);
        
        $result = $this->groupChat->setCorp(null);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertNull($this->groupChat->getCorp());
    }

    public function test_setOwner_withValidOwner_setsOwnerCorrectly(): void
    {
        /** @var UserInterface&MockObject $owner */
        $owner = $this->createMock(UserInterface::class);
        
        $result = $this->groupChat->setOwner($owner);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertSame($owner, $this->groupChat->getOwner());
    }

    public function test_setOwner_withNull_setsNull(): void
    {
        /** @var UserInterface&MockObject $owner */
        $owner = $this->createMock(UserInterface::class);
        $this->groupChat->setOwner($owner);
        
        $result = $this->groupChat->setOwner(null);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertNull($this->groupChat->getOwner());
    }

    /**
     * 测试管理员Collection操作
     */
    public function test_addAdmin_withNewAdmin_addsAdminToCollection(): void
    {
        /** @var UserInterface&MockObject $admin */
        $admin = $this->createMock(UserInterface::class);
        
        $result = $this->groupChat->addAdmin($admin);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertTrue($this->groupChat->getAdmins()->contains($admin));
        $this->assertCount(1, $this->groupChat->getAdmins());
    }

    public function test_addAdmin_withExistingAdmin_doesNotAddDuplicate(): void
    {
        /** @var UserInterface&MockObject $admin */
        $admin = $this->createMock(UserInterface::class);
        
        // 添加第一次
        $this->groupChat->addAdmin($admin);
        $firstCount = $this->groupChat->getAdmins()->count();
        
        // 尝试再次添加相同管理员
        $result = $this->groupChat->addAdmin($admin);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertCount($firstCount, $this->groupChat->getAdmins());
    }

    public function test_addAdmin_withMultipleAdmins_addsAllAdmins(): void
    {
        /** @var UserInterface&MockObject $admin1 */
        $admin1 = $this->createMock(UserInterface::class);
        /** @var UserInterface&MockObject $admin2 */
        $admin2 = $this->createMock(UserInterface::class);
        /** @var UserInterface&MockObject $admin3 */
        $admin3 = $this->createMock(UserInterface::class);
        
        $this->groupChat->addAdmin($admin1);
        $this->groupChat->addAdmin($admin2);
        $this->groupChat->addAdmin($admin3);
        
        $this->assertCount(3, $this->groupChat->getAdmins());
        $this->assertTrue($this->groupChat->getAdmins()->contains($admin1));
        $this->assertTrue($this->groupChat->getAdmins()->contains($admin2));
        $this->assertTrue($this->groupChat->getAdmins()->contains($admin3));
    }

    public function test_removeAdmin_withExistingAdmin_removesAdminFromCollection(): void
    {
        /** @var UserInterface&MockObject $admin */
        $admin = $this->createMock(UserInterface::class);
        
        // 先添加管理员
        $this->groupChat->addAdmin($admin);
        $this->assertCount(1, $this->groupChat->getAdmins());
        
        // 移除管理员
        $result = $this->groupChat->removeAdmin($admin);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertFalse($this->groupChat->getAdmins()->contains($admin));
        $this->assertCount(0, $this->groupChat->getAdmins());
    }

    public function test_removeAdmin_withNonExistingAdmin_doesNothing(): void
    {
        /** @var UserInterface&MockObject $admin */
        $admin = $this->createMock(UserInterface::class);
        
        $result = $this->groupChat->removeAdmin($admin);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertCount(0, $this->groupChat->getAdmins());
    }

    /**
     * 测试成员Collection操作
     */
    public function test_addMember_withNewMember_addsMemberToCollection(): void
    {
        /** @var GroupMember&MockObject $member */
        $member = $this->createMock(GroupMember::class);
        $member->expects($this->once())
               ->method('setGroupChat')
               ->with($this->groupChat);
        
        $result = $this->groupChat->addMember($member);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertTrue($this->groupChat->getMembers()->contains($member));
        $this->assertCount(1, $this->groupChat->getMembers());
    }

    public function test_addMember_withExistingMember_doesNotAddDuplicate(): void
    {
        /** @var GroupMember&MockObject $member */
        $member = $this->createMock(GroupMember::class);
        $member->expects($this->once()) // 只调用一次
               ->method('setGroupChat');
        
        // 添加第一次
        $this->groupChat->addMember($member);
        $firstCount = $this->groupChat->getMembers()->count();
        
        // 尝试再次添加相同成员
        $result = $this->groupChat->addMember($member);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertCount($firstCount, $this->groupChat->getMembers());
    }

    public function test_removeMember_withExistingMember_removesMemberFromCollection(): void
    {
        /** @var GroupMember&MockObject $member */
        $member = $this->createMock(GroupMember::class);
        
        // 设置期望：setGroupChat被调用两次，第一次传入groupChat，第二次传入null
        $member->expects($this->exactly(2))
               ->method('setGroupChat')
               ->with($this->callback(function ($arg) {
                   static $callCount = 0;
                   $callCount++;
                   if ($callCount === 1) {
                       return $arg === $this->groupChat;
                   } else {
                       return $arg === null;
                   }
               }));
        
        $member->expects($this->once())
               ->method('getGroupChat')
               ->willReturn($this->groupChat);
        
        // 先添加成员
        $this->groupChat->addMember($member);
        $this->assertCount(1, $this->groupChat->getMembers());
        
        // 移除成员
        $result = $this->groupChat->removeMember($member);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertFalse($this->groupChat->getMembers()->contains($member));
        $this->assertCount(0, $this->groupChat->getMembers());
    }

    public function test_removeMember_withNonExistingMember_doesNothing(): void
    {
        /** @var GroupMember&MockObject $member */
        $member = $this->createMock(GroupMember::class);
        $member->expects($this->never())->method('setGroupChat');
        $member->expects($this->never())->method('getGroupChat');
        
        $result = $this->groupChat->removeMember($member);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertCount(0, $this->groupChat->getMembers());
    }

    public function test_removeMember_whenMemberGroupChatDiffers_removesButDoesNotSetNull(): void
    {
        /** @var GroupMember&MockObject $member */
        $member = $this->createMock(GroupMember::class);
        /** @var GroupChat&MockObject $otherGroupChat */
        $otherGroupChat = $this->createMock(GroupChat::class);
        
        $member->expects($this->once())->method('setGroupChat')->with($this->groupChat);
        
        // 添加成员
        $this->groupChat->addMember($member);
        
        // 模拟成员的群聊已经被改变
        $member->expects($this->once())->method('getGroupChat')->willReturn($otherGroupChat);
        
        $result = $this->groupChat->removeMember($member);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertFalse($this->groupChat->getMembers()->contains($member));
    }

    /**
     * 测试链式调用
     */
    public function test_chainedSetters_returnSameInstance(): void
    {
        /** @var AgentInterface&MockObject $agent */
        $agent = $this->createMock(AgentInterface::class);
        /** @var CorpInterface&MockObject $corp */
        $corp = $this->createMock(CorpInterface::class);
        /** @var UserInterface&MockObject $owner */
        $owner = $this->createMock(UserInterface::class);
        
        $createTime = new \DateTime('2024-01-15 10:00:00');
        
        $result = $this->groupChat
            ->setChatId('chain_test_chat_id')
            ->setStatus(GroupChatStatus::NORMAL)
            ->setName('链式调用测试群')
            ->setNotice('这是链式调用测试群公告')
            ->setAgent($agent)
            ->setCorp($corp)
            ->setOwner($owner)
            ->setCreateTime($createTime);
        
        $this->assertSame($this->groupChat, $result);
        $this->assertSame('chain_test_chat_id', $this->groupChat->getChatId());
        $this->assertSame(GroupChatStatus::NORMAL, $this->groupChat->getStatus());
        $this->assertSame('链式调用测试群', $this->groupChat->getName());
        $this->assertSame('这是链式调用测试群公告', $this->groupChat->getNotice());
        $this->assertSame($agent, $this->groupChat->getAgent());
        $this->assertSame($corp, $this->groupChat->getCorp());
        $this->assertSame($owner, $this->groupChat->getOwner());
        $this->assertSame($createTime, $this->groupChat->getCreateTime());
    }

    /**
     * 测试边界场景
     */
    public function test_edgeCases_longStrings(): void
    {
        $longString = str_repeat('x', 1000);
        $maxChatId = str_repeat('a', 64);
        $maxName = str_repeat('名', 127); // 中文字符
        
        $this->groupChat->setChatId($maxChatId);
        $this->groupChat->setName($maxName);
        $this->groupChat->setNotice($longString);
        
        $this->assertSame($maxChatId, $this->groupChat->getChatId());
        $this->assertSame($maxName, $this->groupChat->getName());
        $this->assertSame($longString, $this->groupChat->getNotice());
    }

    public function test_edgeCases_dateTimeTypes(): void
    {
        // 测试DateTime
        $dateTime = new \DateTime('2024-01-15 12:30:45');
        $this->groupChat->setCreateTime($dateTime);
        $this->assertSame($dateTime, $this->groupChat->getCreateTime());
        
        // 测试DateTimeImmutable
        $dateTimeImmutable = new \DateTimeImmutable('2024-02-20 09:15:30');
        $this->groupChat->setCreateTime($dateTimeImmutable);
        $this->assertSame($dateTimeImmutable, $this->groupChat->getCreateTime());
    }

    /**
     * 测试Collection操作的复杂场景
     */
    public function test_adminCollection_isIterable(): void
    {
        /** @var UserInterface&MockObject $admin1 */
        $admin1 = $this->createMock(UserInterface::class);
        /** @var UserInterface&MockObject $admin2 */
        $admin2 = $this->createMock(UserInterface::class);
        
        $this->groupChat->addAdmin($admin1);
        $this->groupChat->addAdmin($admin2);
        
        $admins = [];
        foreach ($this->groupChat->getAdmins() as $admin) {
            $admins[] = $admin;
        }
        
        $this->assertCount(2, $admins);
        $this->assertContains($admin1, $admins);
        $this->assertContains($admin2, $admins);
    }

    public function test_memberCollection_isIterable(): void
    {
        /** @var GroupMember&MockObject $member1 */
        $member1 = $this->createMock(GroupMember::class);
        /** @var GroupMember&MockObject $member2 */
        $member2 = $this->createMock(GroupMember::class);
        
        $member1->expects($this->once())->method('setGroupChat');
        $member2->expects($this->once())->method('setGroupChat');
        
        $this->groupChat->addMember($member1);
        $this->groupChat->addMember($member2);
        
        $members = [];
        foreach ($this->groupChat->getMembers() as $member) {
            $members[] = $member;
        }
        
        $this->assertCount(2, $members);
        $this->assertContains($member1, $members);
        $this->assertContains($member2, $members);
    }

    /**
     * 测试业务逻辑场景
     */
    public function test_businessScenario_groupChatLifecycle(): void
    {
        /** @var CorpInterface&MockObject $corp */
        $corp = $this->createMock(CorpInterface::class);
        /** @var UserInterface&MockObject $owner */
        $owner = $this->createMock(UserInterface::class);
        /** @var UserInterface&MockObject $admin */
        $admin = $this->createMock(UserInterface::class);
        
        $createTime = new \DateTime('2024-01-15 10:00:00');
        
        // 创建群聊
        $this->groupChat
            ->setChatId('wrk_lifecycle_test')
            ->setName('生命周期测试群')
            ->setStatus(GroupChatStatus::NORMAL)
            ->setCorp($corp)
            ->setOwner($owner)
            ->setCreateTime($createTime);
        
        // 添加管理员
        $this->groupChat->addAdmin($admin);
        
        // 验证初始状态
        $this->assertSame(GroupChatStatus::NORMAL, $this->groupChat->getStatus());
        $this->assertNotNull($this->groupChat->getOwner());
        $this->assertCount(1, $this->groupChat->getAdmins());
        $this->assertTrue($this->groupChat->getMembers()->isEmpty());
        
        // 模拟跟进人离职
        $this->groupChat->setStatus(GroupChatStatus::RESIGN);
        $this->assertSame(GroupChatStatus::RESIGN, $this->groupChat->getStatus());
        
        // 模拟继承中
        $this->groupChat->setStatus(GroupChatStatus::INHERIT_DOING);
        $this->assertSame(GroupChatStatus::INHERIT_DOING, $this->groupChat->getStatus());
        
        // 模拟继承完成
        $this->groupChat->setStatus(GroupChatStatus::INHERIT_FINISHED);
        $this->assertSame(GroupChatStatus::INHERIT_FINISHED, $this->groupChat->getStatus());
    }

    public function test_businessScenario_adminManagement(): void
    {
        /** @var UserInterface&MockObject $owner */
        $owner = $this->createMock(UserInterface::class);
        /** @var UserInterface&MockObject $admin1 */
        $admin1 = $this->createMock(UserInterface::class);
        /** @var UserInterface&MockObject $admin2 */
        $admin2 = $this->createMock(UserInterface::class);
        /** @var UserInterface&MockObject $admin3 */
        $admin3 = $this->createMock(UserInterface::class);
        
        $this->groupChat->setOwner($owner);
        
        // 添加多个管理员
        $this->groupChat->addAdmin($admin1);
        $this->groupChat->addAdmin($admin2);
        $this->groupChat->addAdmin($admin3);
        
        $this->assertCount(3, $this->groupChat->getAdmins());
        
        // 移除一个管理员
        $this->groupChat->removeAdmin($admin2);
        
        $this->assertCount(2, $this->groupChat->getAdmins());
        $this->assertTrue($this->groupChat->getAdmins()->contains($admin1));
        $this->assertFalse($this->groupChat->getAdmins()->contains($admin2));
        $this->assertTrue($this->groupChat->getAdmins()->contains($admin3));
        
        // 群主不在管理员列表中（这是预期的，因为群主和管理员是分开管理的）
        $this->assertFalse($this->groupChat->getAdmins()->contains($owner));
    }
} 