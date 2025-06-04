<?php

namespace WechatWorkGroupChatBundle\Tests\Entity;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WechatWorkGroupChatBundle\Entity\GroupChat;
use WechatWorkGroupChatBundle\Entity\GroupMember;

/**
 * GroupMember 实体测试用例
 * 
 * 测试客户群成员实体的所有功能
 */
class GroupMemberTest extends TestCase
{
    private GroupMember $groupMember;

    protected function setUp(): void
    {
        $this->groupMember = new GroupMember();
    }

    public function test_constructor_setsDefaultValues(): void
    {
        $member = new GroupMember();
        
        $this->assertNull($member->getId());
        $this->assertNull($member->getGroupChat());
        $this->assertNull($member->getUserId());
        $this->assertNull($member->getType());
        $this->assertNull($member->getJoinTime());
        $this->assertNull($member->getJoinScene());
        $this->assertNull($member->getInvitorUserId());
        $this->assertNull($member->getGroupNickname());
        $this->assertNull($member->getName());
        $this->assertNull($member->getCreateTime());
        $this->assertNull($member->getUpdateTime());
    }

    public function test_setGroupChat_withValidGroupChat_setsGroupChatCorrectly(): void
    {
        /** @var GroupChat&MockObject $groupChat */
        $groupChat = $this->createMock(GroupChat::class);
        
        $result = $this->groupMember->setGroupChat($groupChat);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertSame($groupChat, $this->groupMember->getGroupChat());
    }

    public function test_setGroupChat_withNull_setsNull(): void
    {
        /** @var GroupChat&MockObject $groupChat */
        $groupChat = $this->createMock(GroupChat::class);
        $this->groupMember->setGroupChat($groupChat);
        
        $result = $this->groupMember->setGroupChat(null);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertNull($this->groupMember->getGroupChat());
    }

    public function test_setUserId_withValidUserId_setsUserIdCorrectly(): void
    {
        $userId = 'user123456';
        
        $result = $this->groupMember->setUserId($userId);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertSame($userId, $this->groupMember->getUserId());
    }

    public function test_setUserId_withEmptyString_setsEmptyString(): void
    {
        $result = $this->groupMember->setUserId('');
        
        $this->assertSame($this->groupMember, $result);
        $this->assertSame('', $this->groupMember->getUserId());
    }

    public function test_setUserId_withLongUserId_setsLongUserId(): void
    {
        $longUserId = str_repeat('a', 128); // 最大长度
        
        $result = $this->groupMember->setUserId($longUserId);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertSame($longUserId, $this->groupMember->getUserId());
    }

    public function test_setType_withValidType_setsTypeCorrectly(): void
    {
        $type = 1; // 成员类型，比如：1-企业成员，2-外部联系人
        
        $result = $this->groupMember->setType($type);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertSame($type, $this->groupMember->getType());
    }

    public function test_setType_withDifferentType_setsTypeCorrectly(): void
    {
        $type = 2; // 外部联系人
        
        $result = $this->groupMember->setType($type);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertSame($type, $this->groupMember->getType());
    }

    public function test_setType_withZero_setsZero(): void
    {
        $result = $this->groupMember->setType(0);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertSame(0, $this->groupMember->getType());
    }

    public function test_setType_withNull_setsNull(): void
    {
        $this->groupMember->setType(1);
        
        $result = $this->groupMember->setType(null);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertNull($this->groupMember->getType());
    }

    public function test_setJoinTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $joinTime = new \DateTime('2024-01-15 10:30:00');
        
        $result = $this->groupMember->setJoinTime($joinTime);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertSame($joinTime, $this->groupMember->getJoinTime());
    }

    public function test_setJoinTime_withNull_setsNull(): void
    {
        $this->groupMember->setJoinTime(new \DateTime());
        
        $result = $this->groupMember->setJoinTime(null);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertNull($this->groupMember->getJoinTime());
    }

    public function test_setJoinScene_withValidScene_setsSceneCorrectly(): void
    {
        $joinScene = 1; // 加入场景，比如：1-直接邀请，2-二维码，3-群分享等
        
        $result = $this->groupMember->setJoinScene($joinScene);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertSame($joinScene, $this->groupMember->getJoinScene());
    }

    public function test_setJoinScene_withQrcodeScene_setsSceneCorrectly(): void
    {
        $joinScene = 2; // 二维码加入
        
        $result = $this->groupMember->setJoinScene($joinScene);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertSame($joinScene, $this->groupMember->getJoinScene());
    }

    public function test_setJoinScene_withShareScene_setsSceneCorrectly(): void
    {
        $joinScene = 3; // 群分享加入
        
        $result = $this->groupMember->setJoinScene($joinScene);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertSame($joinScene, $this->groupMember->getJoinScene());
    }

    public function test_setJoinScene_withNull_setsNull(): void
    {
        $this->groupMember->setJoinScene(1);
        
        $result = $this->groupMember->setJoinScene(null);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertNull($this->groupMember->getJoinScene());
    }

    public function test_setInvitorUserId_withValidUserId_setsUserIdCorrectly(): void
    {
        $invitorUserId = 'invitor123456';
        
        $result = $this->groupMember->setInvitorUserId($invitorUserId);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertSame($invitorUserId, $this->groupMember->getInvitorUserId());
    }

    public function test_setInvitorUserId_withNull_setsNull(): void
    {
        $this->groupMember->setInvitorUserId('old_invitor');
        
        $result = $this->groupMember->setInvitorUserId(null);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertNull($this->groupMember->getInvitorUserId());
    }

    public function test_setInvitorUserId_withLongUserId_setsLongUserId(): void
    {
        $longInvitorUserId = str_repeat('b', 128); // 最大长度
        
        $result = $this->groupMember->setInvitorUserId($longInvitorUserId);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertSame($longInvitorUserId, $this->groupMember->getInvitorUserId());
    }

    public function test_setGroupNickname_withValidNickname_setsNicknameCorrectly(): void
    {
        $groupNickname = '产品经理小王';
        
        $result = $this->groupMember->setGroupNickname($groupNickname);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertSame($groupNickname, $this->groupMember->getGroupNickname());
    }

    public function test_setGroupNickname_withNull_setsNull(): void
    {
        $this->groupMember->setGroupNickname('old nickname');
        
        $result = $this->groupMember->setGroupNickname(null);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertNull($this->groupMember->getGroupNickname());
    }

    public function test_setGroupNickname_withLongNickname_setsLongNickname(): void
    {
        $longNickname = str_repeat('昵称', 50); // 长昵称
        
        $result = $this->groupMember->setGroupNickname($longNickname);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertSame($longNickname, $this->groupMember->getGroupNickname());
    }

    public function test_setName_withValidName_setsNameCorrectly(): void
    {
        $name = '张三';
        
        $result = $this->groupMember->setName($name);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertSame($name, $this->groupMember->getName());
    }

    public function test_setName_withNull_setsNull(): void
    {
        $this->groupMember->setName('old name');
        
        $result = $this->groupMember->setName(null);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertNull($this->groupMember->getName());
    }

    public function test_setName_withLongName_setsLongName(): void
    {
        $longName = str_repeat('姓名', 50); // 长姓名
        
        $result = $this->groupMember->setName($longName);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertSame($longName, $this->groupMember->getName());
    }

    public function test_setCreateTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $createTime = new \DateTime('2024-01-01 08:00:00');
        
        $this->groupMember->setCreateTime($createTime);
        
        $this->assertSame($createTime, $this->groupMember->getCreateTime());
    }

    public function test_setCreateTime_withNull_setsNull(): void
    {
        $this->groupMember->setCreateTime(new \DateTime());
        
        $this->groupMember->setCreateTime(null);
        
        $this->assertNull($this->groupMember->getCreateTime());
    }

    public function test_setUpdateTime_withValidDateTime_setsTimeCorrectly(): void
    {
        $updateTime = new \DateTime('2024-01-30 18:30:00');
        
        $this->groupMember->setUpdateTime($updateTime);
        
        $this->assertSame($updateTime, $this->groupMember->getUpdateTime());
    }

    public function test_setUpdateTime_withNull_setsNull(): void
    {
        $this->groupMember->setUpdateTime(new \DateTime());
        
        $this->groupMember->setUpdateTime(null);
        
        $this->assertNull($this->groupMember->getUpdateTime());
    }

    /**
     * 测试链式调用
     */
    public function test_chainedSetters_returnSameInstance(): void
    {
        /** @var GroupChat&MockObject $groupChat */
        $groupChat = $this->createMock(GroupChat::class);
        
        $joinTime = new \DateTime('2024-01-15 10:00:00');
        $createTime = new \DateTime('2024-01-01 08:00:00');
        $updateTime = new \DateTime('2024-01-30 18:00:00');
        
        $result = $this->groupMember
            ->setGroupChat($groupChat)
            ->setUserId('chain_user_123')
            ->setType(1)
            ->setJoinTime($joinTime)
            ->setJoinScene(2)
            ->setInvitorUserId('invitor_456')
            ->setGroupNickname('链式测试昵称')
            ->setName('链式测试姓名');
        
        $this->groupMember->setCreateTime($createTime);
        $this->groupMember->setUpdateTime($updateTime);
        
        $this->assertSame($this->groupMember, $result);
        $this->assertSame($groupChat, $this->groupMember->getGroupChat());
        $this->assertSame('chain_user_123', $this->groupMember->getUserId());
        $this->assertSame(1, $this->groupMember->getType());
        $this->assertSame($joinTime, $this->groupMember->getJoinTime());
        $this->assertSame(2, $this->groupMember->getJoinScene());
        $this->assertSame('invitor_456', $this->groupMember->getInvitorUserId());
        $this->assertSame('链式测试昵称', $this->groupMember->getGroupNickname());
        $this->assertSame('链式测试姓名', $this->groupMember->getName());
        $this->assertSame($createTime, $this->groupMember->getCreateTime());
        $this->assertSame($updateTime, $this->groupMember->getUpdateTime());
    }

    /**
     * 测试边界场景
     */
    public function test_edgeCases_extremeValues(): void
    {
        // 测试极端整数值
        $this->groupMember->setType(PHP_INT_MAX);
        $this->assertSame(PHP_INT_MAX, $this->groupMember->getType());
        
        $this->groupMember->setType(PHP_INT_MIN);
        $this->assertSame(PHP_INT_MIN, $this->groupMember->getType());
        
        $this->groupMember->setJoinScene(PHP_INT_MAX);
        $this->assertSame(PHP_INT_MAX, $this->groupMember->getJoinScene());
        
        $this->groupMember->setJoinScene(PHP_INT_MIN);
        $this->assertSame(PHP_INT_MIN, $this->groupMember->getJoinScene());
    }

    public function test_edgeCases_longStrings(): void
    {
        $maxUserId = str_repeat('a', 128);
        $maxInvitorUserId = str_repeat('b', 128);
        $maxGroupNickname = str_repeat('昵', 50);
        $maxName = str_repeat('名', 50);
        
        $this->groupMember->setUserId($maxUserId);
        $this->groupMember->setInvitorUserId($maxInvitorUserId);
        $this->groupMember->setGroupNickname($maxGroupNickname);
        $this->groupMember->setName($maxName);
        
        $this->assertSame($maxUserId, $this->groupMember->getUserId());
        $this->assertSame($maxInvitorUserId, $this->groupMember->getInvitorUserId());
        $this->assertSame($maxGroupNickname, $this->groupMember->getGroupNickname());
        $this->assertSame($maxName, $this->groupMember->getName());
    }

    public function test_edgeCases_dateTimeTypes(): void
    {
        // 测试DateTime
        $dateTime = new \DateTime('2024-01-15 12:30:45');
        $this->groupMember->setJoinTime($dateTime);
        $this->assertSame($dateTime, $this->groupMember->getJoinTime());
        
        // 测试DateTimeImmutable
        $dateTimeImmutable = new \DateTimeImmutable('2024-02-20 09:15:30');
        $this->groupMember->setJoinTime($dateTimeImmutable);
        $this->assertSame($dateTimeImmutable, $this->groupMember->getJoinTime());
        
        // 测试不同时区的DateTime
        $dateTimeUtc = new \DateTime('2024-03-15 14:30:00', new \DateTimeZone('UTC'));
        $this->groupMember->setJoinTime($dateTimeUtc);
        $this->assertSame($dateTimeUtc, $this->groupMember->getJoinTime());
        $this->assertEquals('UTC', $dateTimeUtc->getTimezone()->getName());
    }

    /**
     * 测试业务逻辑场景
     */
    public function test_businessScenario_memberJoinedByInvitation(): void
    {
        /** @var GroupChat&MockObject $groupChat */
        $groupChat = $this->createMock(GroupChat::class);
        
        $joinTime = new \DateTime('2024-01-15 14:30:00');
        $createTime = new \DateTime('2024-01-15 14:30:01');
        
        // 模拟通过邀请加入群聊的场景
        $this->groupMember
            ->setGroupChat($groupChat)
            ->setUserId('invited_user_123')
            ->setType(2) // 外部联系人
            ->setJoinTime($joinTime)
            ->setJoinScene(1) // 直接邀请
            ->setInvitorUserId('admin_456')
            ->setGroupNickname('新加入的客户')
            ->setName('李四');
        
        $this->groupMember->setCreateTime($createTime);
        
        // 验证邀请加入的状态
        $this->assertNotNull($this->groupMember->getGroupChat());
        $this->assertSame(2, $this->groupMember->getType()); // 外部联系人
        $this->assertSame(1, $this->groupMember->getJoinScene()); // 直接邀请
        $this->assertNotNull($this->groupMember->getInvitorUserId()); // 有邀请人
        $this->assertNotNull($this->groupMember->getJoinTime());
        
        // 验证时间逻辑
        $this->assertTrue($joinTime <= $createTime);
    }

    public function test_businessScenario_memberJoinedByQrcode(): void
    {
        /** @var GroupChat&MockObject $groupChat */
        $groupChat = $this->createMock(GroupChat::class);
        
        $joinTime = new \DateTime('2024-01-16 09:20:00');
        
        // 模拟通过二维码加入群聊的场景
        $this->groupMember
            ->setGroupChat($groupChat)
            ->setUserId('qrcode_user_789')
            ->setType(2) // 外部联系人
            ->setJoinTime($joinTime)
            ->setJoinScene(2) // 二维码加入
            ->setInvitorUserId(null) // 二维码加入通常没有直接邀请人
            ->setGroupNickname('二维码扫码客户')
            ->setName('王五');
        
        // 验证二维码加入的状态
        $this->assertSame(2, $this->groupMember->getJoinScene()); // 二维码加入
        $this->assertNull($this->groupMember->getInvitorUserId()); // 没有直接邀请人
        $this->assertNotNull($this->groupMember->getJoinTime());
        $this->assertNotNull($this->groupMember->getGroupNickname());
    }

    public function test_businessScenario_enterpriseMember(): void
    {
        /** @var GroupChat&MockObject $groupChat */
        $groupChat = $this->createMock(GroupChat::class);
        
        $joinTime = new \DateTime('2024-01-10 08:00:00');
        
        // 模拟企业内部成员加入群聊的场景
        $this->groupMember
            ->setGroupChat($groupChat)
            ->setUserId('enterprise_user_001')
            ->setType(1) // 企业成员
            ->setJoinTime($joinTime)
            ->setJoinScene(1) // 直接邀请（管理员拉入）
            ->setInvitorUserId('admin_001')
            ->setGroupNickname('产品经理')
            ->setName('张经理');
        
        // 验证企业成员的状态
        $this->assertSame(1, $this->groupMember->getType()); // 企业成员
        $this->assertNotNull($this->groupMember->getInvitorUserId()); // 有邀请人（管理员）
        $this->assertSame('产品经理', $this->groupMember->getGroupNickname());
        $this->assertSame('张经理', $this->groupMember->getName());
    }

    public function test_businessScenario_memberWithoutOptionalFields(): void
    {
        /** @var GroupChat&MockObject $groupChat */
        $groupChat = $this->createMock(GroupChat::class);
        
        // 模拟只有必要字段的成员
        $this->groupMember
            ->setGroupChat($groupChat)
            ->setUserId('minimal_user_999');
        
        // 验证最小配置的成员状态
        $this->assertNotNull($this->groupMember->getGroupChat());
        $this->assertNotNull($this->groupMember->getUserId());
        $this->assertNull($this->groupMember->getType());
        $this->assertNull($this->groupMember->getJoinTime());
        $this->assertNull($this->groupMember->getJoinScene());
        $this->assertNull($this->groupMember->getInvitorUserId());
        $this->assertNull($this->groupMember->getGroupNickname());
        $this->assertNull($this->groupMember->getName());
    }

    public function test_businessScenario_memberTimeSequence(): void
    {
        $joinTime = new \DateTime('2024-01-15 10:00:00');
        $createTime = new \DateTime('2024-01-15 10:00:01');
        $updateTime = new \DateTime('2024-01-20 15:30:00');
        
        $this->groupMember->setJoinTime($joinTime);
        $this->groupMember->setCreateTime($createTime);
        $this->groupMember->setUpdateTime($updateTime);
        
        // 验证时间序列合理性
        $this->assertTrue($joinTime <= $createTime); // 加入时间应该早于或等于创建时间
        $this->assertTrue($createTime <= $updateTime); // 创建时间应该早于或等于更新时间
        
        $this->assertSame($joinTime, $this->groupMember->getJoinTime());
        $this->assertSame($createTime, $this->groupMember->getCreateTime());
        $this->assertSame($updateTime, $this->groupMember->getUpdateTime());
    }

    /**
     * 测试关联关系
     */
    public function test_groupChatRelation_bidirectional(): void
    {
        /** @var GroupChat&MockObject $groupChat */
        $groupChat = $this->createMock(GroupChat::class);
        
        $this->groupMember->setGroupChat($groupChat);
        
        $this->assertSame($groupChat, $this->groupMember->getGroupChat());
    }

    public function test_groupChatRelation_nullifyCorrectly(): void
    {
        /** @var GroupChat&MockObject $groupChat */
        $groupChat = $this->createMock(GroupChat::class);
        
        $this->groupMember->setGroupChat($groupChat);
        $this->assertSame($groupChat, $this->groupMember->getGroupChat());
        
        $this->groupMember->setGroupChat(null);
        $this->assertNull($this->groupMember->getGroupChat());
    }
} 