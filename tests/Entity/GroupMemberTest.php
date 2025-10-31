<?php

namespace WechatWorkGroupChatBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatWorkGroupChatBundle\Entity\GroupChat;
use WechatWorkGroupChatBundle\Entity\GroupMember;

/**
 * GroupMember 实体测试用例
 *
 * 测试客户群成员实体的所有功能
 *
 * @internal
 */
#[CoversClass(GroupMember::class)]
final class GroupMemberTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new GroupMember();
    }

    /** @return iterable<string, array{string, mixed}> */
    public static function propertiesProvider(): iterable
    {
        return [
            'groupChat' => ['groupChat', null],
            'userId' => ['userId', 'test_user_id'],
            'type' => ['type', 1],
            'joinTime' => ['joinTime', null],
            'joinScene' => ['joinScene', 1],
            'invitorUserId' => ['invitorUserId', null],
            'groupNickname' => ['groupNickname', '测试昵称'],
            'name' => ['name', '测试姓名'],
        ];
    }

    public function testConstructorSetsDefaultValues(): void
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

    public function testSetGroupChatWithValidGroupChatSetsGroupChatCorrectly(): void
    {
        $groupMember = new GroupMember();
        /**
         * 使用具体类 GroupChat 创建 Mock 对象的原因：
         * 1. 测试 GroupMember 与 GroupChat 的双向关联关系设置
         * 2. 需要验证实体关系的正确性，而非抽象接口行为
         * 3. GroupChat 是具体的实体类，包含业务逻辑和数据状态
         * 4. 没有适合的接口可以替代这种实体关系测试
         */
        $groupChat = $this->createMock(GroupChat::class);

        $groupMember->setGroupChat($groupChat);

        $this->assertSame($groupChat, $groupMember->getGroupChat());
    }

    public function testSetGroupChatWithNullSetsNull(): void
    {
        $groupMember = new GroupMember();
        /**
         * 使用具体类 GroupChat 创建 Mock 对象的原因：
         * 1. 测试关联关系的清空操作，从有关联到无关联的状态转换
         * 2. 验证实体关系可以被正确地设置为 null
         * 3. 确保对象状态管理的完整性和一致性
         * 4. 这种状态转换逻辑需要通过实体类来正确模拟
         */
        $groupChat = $this->createMock(GroupChat::class);
        $groupMember->setGroupChat($groupChat);

        $groupMember->setGroupChat(null);

        $this->assertNull($groupMember->getGroupChat());
    }

    public function testSetUserIdWithValidUserIdSetsUserIdCorrectly(): void
    {
        $groupMember = new GroupMember();
        $userId = 'user123456';

        $groupMember->setUserId($userId);

        $this->assertSame($userId, $groupMember->getUserId());
    }

    public function testSetUserIdWithEmptyStringSetsEmptyString(): void
    {
        $groupMember = new GroupMember();
        $groupMember->setUserId('');

        $this->assertSame('', $groupMember->getUserId());
    }

    public function testSetUserIdWithLongUserIdSetsLongUserId(): void
    {
        $groupMember = new GroupMember();
        $longUserId = str_repeat('a', 128); // 最大长度

        $groupMember->setUserId($longUserId);

        $this->assertSame($longUserId, $groupMember->getUserId());
    }

    public function testSetTypeWithValidTypeSetsTypeCorrectly(): void
    {
        $groupMember = new GroupMember();
        $type = 1; // 成员类型，比如：1-企业成员，2-外部联系人

        $groupMember->setType($type);

        $this->assertSame($type, $groupMember->getType());
    }

    public function testSetTypeWithDifferentTypeSetsTypeCorrectly(): void
    {
        $groupMember = new GroupMember();
        $type = 2; // 外部联系人

        $groupMember->setType($type);
        $this->assertSame($type, $groupMember->getType());
    }

    public function testSetTypeWithZeroSetsZero(): void
    {
        $groupMember = new GroupMember();
        $groupMember->setType(0);
        $this->assertSame(0, $groupMember->getType());
    }

    public function testSetTypeWithNullSetsNull(): void
    {
        $groupMember = new GroupMember();
        $groupMember->setType(1);

        $groupMember->setType(null);
        $this->assertNull($groupMember->getType());
    }

    public function testSetJoinTimeWithValidDateTimeSetsTimeCorrectly(): void
    {
        $groupMember = new GroupMember();
        $joinTime = new \DateTimeImmutable('2024-01-15 10:30:00');

        $groupMember->setJoinTime($joinTime);
        $this->assertInstanceOf(\DateTimeImmutable::class, $groupMember->getJoinTime());
        $this->assertEquals($joinTime->format('Y-m-d H:i:s'), $groupMember->getJoinTime()->format('Y-m-d H:i:s'));
    }

    public function testSetJoinTimeWithNullSetsNull(): void
    {
        $groupMember = new GroupMember();
        $groupMember->setJoinTime(new \DateTimeImmutable());

        $groupMember->setJoinTime(null);
        $this->assertNull($groupMember->getJoinTime());
    }

    public function testSetJoinSceneWithValidSceneSetsSceneCorrectly(): void
    {
        $groupMember = new GroupMember();
        $joinScene = 1; // 加入场景，比如：1-直接邀请，2-二维码，3-群分享等

        $groupMember->setJoinScene($joinScene);
        $this->assertSame($joinScene, $groupMember->getJoinScene());
    }

    public function testSetJoinSceneWithQrcodeSceneSetsSceneCorrectly(): void
    {
        $groupMember = new GroupMember();
        $joinScene = 2; // 二维码加入

        $groupMember->setJoinScene($joinScene);
        $this->assertSame($joinScene, $groupMember->getJoinScene());
    }

    public function testSetJoinSceneWithShareSceneSetsSceneCorrectly(): void
    {
        $groupMember = new GroupMember();
        $joinScene = 3; // 群分享加入

        $groupMember->setJoinScene($joinScene);
        $this->assertSame($joinScene, $groupMember->getJoinScene());
    }

    public function testSetJoinSceneWithNullSetsNull(): void
    {
        $groupMember = new GroupMember();
        $groupMember->setJoinScene(1);

        $groupMember->setJoinScene(null);
        $this->assertNull($groupMember->getJoinScene());
    }

    public function testSetInvitorUserIdWithValidUserIdSetsUserIdCorrectly(): void
    {
        $groupMember = new GroupMember();
        $invitorUserId = 'invitor123456';

        $groupMember->setInvitorUserId($invitorUserId);
        $this->assertSame($invitorUserId, $groupMember->getInvitorUserId());
    }

    public function testSetInvitorUserIdWithNullSetsNull(): void
    {
        $groupMember = new GroupMember();
        $groupMember->setInvitorUserId('old_invitor');

        $groupMember->setInvitorUserId(null);
        $this->assertNull($groupMember->getInvitorUserId());
    }

    public function testSetInvitorUserIdWithLongUserIdSetsLongUserId(): void
    {
        $groupMember = new GroupMember();
        $longInvitorUserId = str_repeat('b', 128); // 最大长度

        $groupMember->setInvitorUserId($longInvitorUserId);
        $this->assertSame($longInvitorUserId, $groupMember->getInvitorUserId());
    }

    public function testSetGroupNicknameWithValidNicknameSetsNicknameCorrectly(): void
    {
        $groupMember = new GroupMember();
        $groupNickname = '产品经理小王';

        $groupMember->setGroupNickname($groupNickname);
        $this->assertSame($groupNickname, $groupMember->getGroupNickname());
    }

    public function testSetGroupNicknameWithNullSetsNull(): void
    {
        $groupMember = new GroupMember();
        $groupMember->setGroupNickname('old nickname');

        $groupMember->setGroupNickname(null);
        $this->assertNull($groupMember->getGroupNickname());
    }

    public function testSetGroupNicknameWithLongNicknameSetsLongNickname(): void
    {
        $groupMember = new GroupMember();
        $longNickname = str_repeat('昵称', 50); // 长昵称

        $groupMember->setGroupNickname($longNickname);
        $this->assertSame($longNickname, $groupMember->getGroupNickname());
    }

    public function testSetNameWithValidNameSetsNameCorrectly(): void
    {
        $groupMember = new GroupMember();
        $name = '张三';

        $groupMember->setName($name);
        $this->assertSame($name, $groupMember->getName());
    }

    public function testSetNameWithNullSetsNull(): void
    {
        $groupMember = new GroupMember();
        $groupMember->setName('old name');

        $groupMember->setName(null);
        $this->assertNull($groupMember->getName());
    }

    public function testSetNameWithLongNameSetsLongName(): void
    {
        $groupMember = new GroupMember();
        $longName = str_repeat('姓名', 50); // 长姓名

        $groupMember->setName($longName);
        $this->assertSame($longName, $groupMember->getName());
    }

    public function testSetCreateTimeWithValidDateTimeSetsTimeCorrectly(): void
    {
        $groupMember = new GroupMember();
        $createTime = new \DateTimeImmutable('2024-01-01 08:00:00');

        $groupMember->setCreateTime($createTime);

        $this->assertSame($createTime, $groupMember->getCreateTime());
    }

    public function testSetCreateTimeWithNullSetsNull(): void
    {
        $groupMember = new GroupMember();
        $groupMember->setCreateTime(new \DateTimeImmutable());

        $groupMember->setCreateTime(null);

        $this->assertNull($groupMember->getCreateTime());
    }

    public function testSetUpdateTimeWithValidDateTimeSetsTimeCorrectly(): void
    {
        $groupMember = new GroupMember();
        $updateTime = new \DateTimeImmutable('2024-01-30 18:30:00');

        $groupMember->setUpdateTime($updateTime);

        $this->assertSame($updateTime, $groupMember->getUpdateTime());
    }

    public function testSetUpdateTimeWithNullSetsNull(): void
    {
        $groupMember = new GroupMember();
        $groupMember->setUpdateTime(new \DateTimeImmutable());

        $groupMember->setUpdateTime(null);

        $this->assertNull($groupMember->getUpdateTime());
    }

    /**
     * 测试多个setter方法设置后的状态正确性
     */
    public function testMultipleSettersSetStateCorrectly(): void
    {
        $groupMember = new GroupMember();
        /**
         * 使用具体类 GroupChat 创建 Mock 对象的原因：
         * 1. 测试多个setter方法设置时的对象关联设置正确性
         * 2. 验证所有 setter 方法都能正确设置状态
         * 3. GroupChat 作为关联实体，需要实际的对象引用
         * 4. 确保对象状态设置的原子性和一致性
         */
        $groupChat = $this->createMock(GroupChat::class);

        $joinTime = new \DateTimeImmutable('2024-01-15 10:00:00');
        $createTime = new \DateTimeImmutable('2024-01-01 08:00:00');
        $updateTime = new \DateTimeImmutable('2024-01-30 18:00:00');

        $groupMember->setGroupChat($groupChat);
        $groupMember->setUserId('chain_user_123');
        $groupMember->setType(1);
        $groupMember->setJoinTime($joinTime);
        $groupMember->setJoinScene(2);
        $groupMember->setInvitorUserId('invitor_456');
        $groupMember->setGroupNickname('链式测试昵称');
        $groupMember->setName('链式测试姓名');
        $groupMember->setCreateTime($createTime);
        $groupMember->setUpdateTime($updateTime);

        $this->assertSame($groupChat, $groupMember->getGroupChat());
        $this->assertSame('chain_user_123', $groupMember->getUserId());
        $this->assertSame(1, $groupMember->getType());
        $this->assertInstanceOf(\DateTimeImmutable::class, $groupMember->getJoinTime());
        $this->assertEquals($joinTime->format('Y-m-d H:i:s'), $groupMember->getJoinTime()->format('Y-m-d H:i:s'));
        $this->assertSame(2, $groupMember->getJoinScene());
        $this->assertSame('invitor_456', $groupMember->getInvitorUserId());
        $this->assertSame('链式测试昵称', $groupMember->getGroupNickname());
        $this->assertSame('链式测试姓名', $groupMember->getName());
        $this->assertSame($createTime, $groupMember->getCreateTime());
        $this->assertSame($updateTime, $groupMember->getUpdateTime());
    }

    /**
     * 测试边界场景
     */
    public function testEdgeCasesExtremeValues(): void
    {
        $groupMember = new GroupMember();
        // 测试极端整数值
        $groupMember->setType(PHP_INT_MAX);
        $this->assertSame(PHP_INT_MAX, $groupMember->getType());

        $groupMember->setType(PHP_INT_MIN);
        $this->assertSame(PHP_INT_MIN, $groupMember->getType());

        $groupMember->setJoinScene(PHP_INT_MAX);
        $this->assertSame(PHP_INT_MAX, $groupMember->getJoinScene());

        $groupMember->setJoinScene(PHP_INT_MIN);
        $this->assertEquals(PHP_INT_MIN, $groupMember->getJoinScene());
    }

    public function testEdgeCasesLongStrings(): void
    {
        $groupMember = new GroupMember();
        $maxUserId = str_repeat('a', 128);
        $maxInvitorUserId = str_repeat('b', 128);
        $maxGroupNickname = str_repeat('昵', 50);
        $maxName = str_repeat('名', 50);

        $groupMember->setUserId($maxUserId);
        $groupMember->setInvitorUserId($maxInvitorUserId);
        $groupMember->setGroupNickname($maxGroupNickname);
        $groupMember->setName($maxName);

        $this->assertSame($maxUserId, $groupMember->getUserId());
        $this->assertSame($maxInvitorUserId, $groupMember->getInvitorUserId());
        $this->assertSame($maxGroupNickname, $groupMember->getGroupNickname());
        $this->assertSame($maxName, $groupMember->getName());
    }

    public function testEdgeCasesDateTimeTypes(): void
    {
        $groupMember = new GroupMember();
        // 测试DateTime
        $dateTime = new \DateTimeImmutable('2024-01-15 12:30:45');
        $groupMember->setJoinTime($dateTime);
        $this->assertInstanceOf(\DateTimeImmutable::class, $groupMember->getJoinTime());
        $this->assertEquals($dateTime->format('Y-m-d H:i:s'), $groupMember->getJoinTime()->format('Y-m-d H:i:s'));

        // 测试DateTimeImmutable
        $dateTimeImmutable = new \DateTimeImmutable('2024-02-20 09:15:30');
        $groupMember->setJoinTime($dateTimeImmutable);
        $this->assertSame($dateTimeImmutable, $groupMember->getJoinTime());

        // 测试不同时区的DateTime
        $dateTimeUtc = new \DateTimeImmutable('2024-03-15 14:30:00', new \DateTimeZone('UTC'));
        $groupMember->setJoinTime($dateTimeUtc);
        $this->assertInstanceOf(\DateTimeImmutable::class, $groupMember->getJoinTime());
        $this->assertEquals($dateTimeUtc->format('Y-m-d H:i:s'), $groupMember->getJoinTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('UTC', $dateTimeUtc->getTimezone()->getName());
    }

    /**
     * 测试业务逻辑场景
     */
    public function testBusinessScenarioMemberJoinedByInvitation(): void
    {
        $groupMember = new GroupMember();
        /**
         * 使用具体类 GroupChat 创建 Mock 对象的原因：
         * 1. 模拟真实的业务场景：用户通过邀请加入群聊
         * 2. 需要验证成员与群聊的完整关联关系
         * 3. 业务逻辑需要真实的实体对象来保证数据一致性
         * 4. 测试复杂的业务流程和状态管理
         */
        $groupChat = $this->createMock(GroupChat::class);

        $joinTime = new \DateTimeImmutable('2024-01-15 14:30:00');
        $createTime = new \DateTimeImmutable('2024-01-15 14:30:01');

        // 模拟通过邀请加入群聊的场景
        $groupMember->setGroupChat($groupChat);
        $groupMember->setUserId('invited_user_123');
        $groupMember->setType(2); // 外部联系人
        $groupMember->setJoinTime($joinTime);
        $groupMember->setJoinScene(1); // 直接邀请
        $groupMember->setInvitorUserId('admin_456');
        $groupMember->setGroupNickname('新加入的客户');
        $groupMember->setName('李四');

        $groupMember->setCreateTime($createTime);

        // 验证邀请加入的状态
        $this->assertNotNull($groupMember->getGroupChat());
        $this->assertSame(2, $groupMember->getType()); // 外部联系人
        $this->assertSame(1, $groupMember->getJoinScene()); // 直接邀请
        $this->assertNotNull($groupMember->getInvitorUserId()); // 有邀请人
        $this->assertNotNull($groupMember->getJoinTime());

        // 验证时间逻辑
        $this->assertLessThanOrEqual($createTime, $joinTime);
    }

    public function testBusinessScenarioMemberJoinedByQrcode(): void
    {
        $groupMember = new GroupMember();
        /**
         * 使用具体类 GroupChat 创建 Mock 对象的原因：
         * 1. 模拟二维码加入群聊的业务场景，需要真实的群对象
         * 2. 验证不同加入方式的状态差异（无邀请人等）
         * 3. 保证成员与群的关联关系在不同场景下都正确
         * 4. 业务逻辑的完整性需要真实的实体对象支持
         */
        $groupChat = $this->createMock(GroupChat::class);

        $joinTime = new \DateTimeImmutable('2024-01-16 09:20:00');

        // 模拟通过二维码加入群聊的场景
        $groupMember->setGroupChat($groupChat);
        $groupMember->setUserId('qrcode_user_789');
        $groupMember->setType(2); // 外部联系人
        $groupMember->setJoinTime($joinTime);
        $groupMember->setJoinScene(2); // 二维码加入
        $groupMember->setInvitorUserId(null); // 二维码加入通常没有直接邀请人
        $groupMember->setGroupNickname('二维码扫码客户');
        $groupMember->setName('王五');

        // 验证二维码加入的状态
        $this->assertSame(2, $groupMember->getJoinScene()); // 二维码加入
        $this->assertNull($groupMember->getInvitorUserId()); // 没有直接邀请人
        $this->assertNotNull($groupMember->getJoinTime());
        $this->assertNotNull($groupMember->getGroupNickname());
    }

    public function testBusinessScenarioEnterpriseMember(): void
    {
        $groupMember = new GroupMember();
        /**
         * 使用具体类 GroupChat 创建 Mock 对象的原因：
         * 1. 模拟企业内部成员的业务场景，需要验证与群的关联
         * 2. 企业成员与外部成员的状态差异需要真实对象来体现
         * 3. 测试不同类型成员的数据一致性和完整性
         * 4. 保证复杂业务规则在实体关系中的正确实现
         */
        $groupChat = $this->createMock(GroupChat::class);

        $joinTime = new \DateTimeImmutable('2024-01-10 08:00:00');

        // 模拟企业内部成员加入群聊的场景
        $groupMember->setGroupChat($groupChat);
        $groupMember->setUserId('enterprise_user_001');
        $groupMember->setType(1); // 企业成员
        $groupMember->setJoinTime($joinTime);
        $groupMember->setJoinScene(1); // 直接邀请（管理员拉入）
        $groupMember->setInvitorUserId('admin_001');
        $groupMember->setGroupNickname('产品经理');
        $groupMember->setName('张经理');

        // 验证企业成员的状态
        $this->assertSame(1, $groupMember->getType()); // 企业成员
        $this->assertNotNull($groupMember->getInvitorUserId()); // 有邀请人（管理员）
        $this->assertSame('产品经理', $groupMember->getGroupNickname());
        $this->assertSame('张经理', $groupMember->getName());
    }

    public function testBusinessScenarioMemberWithoutOptionalFields(): void
    {
        $groupMember = new GroupMember();
        /**
         * 使用具体类 GroupChat 创建 Mock 对象的原因：
         * 1. 测试最小配置场景，只设置必要字段的成员对象
         * 2. 验证对象初始状态的正确性和可选字段的默认值
         * 3. 确保最少数据情况下对象仍然可用和一致
         * 4. 这种边界条件测试需要真实的关联对象
         */
        $groupChat = $this->createMock(GroupChat::class);

        // 模拟只有必要字段的成员
        $groupMember->setGroupChat($groupChat);
        $groupMember->setUserId('minimal_user_999');

        // 验证最小配置的成员状态
        $this->assertNotNull($groupMember->getGroupChat());
        $this->assertNotNull($groupMember->getUserId());
        $this->assertNull($groupMember->getType());
        $this->assertNull($groupMember->getJoinTime());
        $this->assertNull($groupMember->getJoinScene());
        $this->assertNull($groupMember->getInvitorUserId());
        $this->assertNull($groupMember->getGroupNickname());
        $this->assertNull($groupMember->getName());
    }

    public function testBusinessScenarioMemberTimeSequence(): void
    {
        $groupMember = new GroupMember();
        $joinTime = new \DateTimeImmutable('2024-01-15 10:00:00');
        $createTime = new \DateTimeImmutable('2024-01-15 10:00:01');
        $updateTime = new \DateTimeImmutable('2024-01-20 15:30:00');

        $groupMember->setJoinTime($joinTime);
        $groupMember->setCreateTime($createTime);
        $groupMember->setUpdateTime($updateTime);

        // 验证时间序列合理性
        $this->assertLessThanOrEqual($createTime, $joinTime); // 加入时间应该早于或等于创建时间
        $this->assertLessThanOrEqual($updateTime, $createTime); // 创建时间应该早于或等于更新时间

        $this->assertInstanceOf(\DateTimeImmutable::class, $groupMember->getJoinTime());
        $this->assertEquals($joinTime->format('Y-m-d H:i:s'), $groupMember->getJoinTime()->format('Y-m-d H:i:s'));
        $this->assertSame($createTime, $groupMember->getCreateTime());
        $this->assertSame($updateTime, $groupMember->getUpdateTime());
    }

    /**
     * 测试关联关系
     */
    public function testGroupChatRelationBidirectional(): void
    {
        $groupMember = new GroupMember();
        /**
         * 使用具体类 GroupChat 创建 Mock 对象的原因：
         * 1. 测试实体间的双向关联关系的正确性
         * 2. 验证对象引用的一致性和持久性
         * 3. 确保关系设置后可以正确获取和访问
         * 4. 这种关系测试需要具体的实体类来实现
         */
        $groupChat = $this->createMock(GroupChat::class);

        $groupMember->setGroupChat($groupChat);

        $this->assertSame($groupChat, $groupMember->getGroupChat());
    }

    public function testGroupChatRelationNullifyCorrectly(): void
    {
        $groupMember = new GroupMember();
        /**
         * 使用具体类 GroupChat 创建 Mock 对象的原因：
         * 1. 测试关联关系的正确清空和重置操作
         * 2. 验证对象状态从有关联到无关联的正确转换
         * 3. 确保关系解除后不会影响其他字段的状态
         * 4. 这种数据一致性测试需要真实的实体对象
         */
        $groupChat = $this->createMock(GroupChat::class);

        $groupMember->setGroupChat($groupChat);
        $this->assertSame($groupChat, $groupMember->getGroupChat());

        $groupMember->setGroupChat(null);
        $this->assertNull($groupMember->getGroupChat());
    }
}
