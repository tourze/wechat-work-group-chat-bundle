<?php

namespace WechatWorkGroupChatBundle\Tests\MessageHandler;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Tourze\WechatWorkContracts\UserInterface;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Service\WorkService;
use WechatWorkGroupChatBundle\Entity\GroupChat;
use WechatWorkGroupChatBundle\Entity\GroupMember;
use WechatWorkGroupChatBundle\Message\SyncGroupChatDetailMessage;
use WechatWorkGroupChatBundle\MessageHandler\SyncGroupChatDetailHandler;
use WechatWorkGroupChatBundle\Repository\GroupChatRepository;

class SyncGroupChatDetailHandlerTest extends TestCase
{
    private GroupChatRepository&MockObject $groupChatRepository;
    private WorkService&MockObject $workService;
    private UserLoaderInterface&MockObject $userLoader;
    private PropertyAccessor&MockObject $propertyAccessor;
    private EntityManagerInterface&MockObject $entityManager;
    private SyncGroupChatDetailHandler $handler;

    protected function setUp(): void
    {
        $this->groupChatRepository = $this->createMock(GroupChatRepository::class);
        $this->workService = $this->createMock(WorkService::class);
        $this->userLoader = $this->createMock(UserLoaderInterface::class);
        $this->propertyAccessor = $this->createMock(PropertyAccessor::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->handler = new SyncGroupChatDetailHandler(
            $this->groupChatRepository,
            $this->workService,
            $this->userLoader,
            $this->propertyAccessor,
            $this->entityManager
        );
    }

    public function testInvokeWithGroupNotFound(): void
    {
        // 准备数据
        $message = new SyncGroupChatDetailMessage();
        $message->setChatId('chat123');

        $this->groupChatRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['chatId' => 'chat123'])
            ->willReturn(null);

        // 验证异常
        $this->expectException(UnrecoverableMessageHandlingException::class);
        $this->expectExceptionMessage('数据库中找不到客户群信息');

        // 执行测试
        ($this->handler)($message);
    }

    public function testInvokeWithApiResponseMissingGroupChat(): void
    {
        // 准备数据
        $message = new SyncGroupChatDetailMessage();
        $message->setChatId('chat123');

        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);
        
        $groupChat = new GroupChat();
        $groupChat->setChatId('chat123');
        $groupChat->setAgent($agent);
        $groupChat->setCorp($corp);

        $this->groupChatRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['chatId' => 'chat123'])
            ->willReturn($groupChat);

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn([]); // 没有 group_chat 字段

        // 验证异常
        $this->expectException(UnrecoverableMessageHandlingException::class);
        $this->expectExceptionMessage('接口中找不到客户群信息');

        // 执行测试
        ($this->handler)($message);
    }

    public function testInvokeWithBasicGroupChatData(): void
    {
        // 准备数据
        $message = new SyncGroupChatDetailMessage();
        $message->setChatId('chat123');

        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);
        
        $groupChat = new GroupChat();
        $groupChat->setChatId('chat123');
        $groupChat->setAgent($agent);
        $groupChat->setCorp($corp);

        $this->groupChatRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['chatId' => 'chat123'])
            ->willReturn($groupChat);

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn([
                'group_chat' => [
                    'name' => '测试群',
                    'notice' => '群公告内容',
                    'create_time' => 1640000000,
                    'owner' => 'owner123',
                    'admin_list' => [],
                    'member_list' => []
                ]
            ]);

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->with('owner123', $corp)
            ->willReturn(null);

        $this->propertyAccessor->expects($this->exactly(2))
            ->method('setValue')
            ->willReturnCallback(function ($object, $property, $value) use ($groupChat) {
                $this->assertSame($groupChat, $object);
                $this->assertContains($property, ['admins', 'members']);
                $this->assertIsArray($value);
                $this->assertEmpty($value);
            });

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($groupChat);

        $this->entityManager->expects($this->once())
            ->method('flush');

        // 执行测试
        ($this->handler)($message);

        // 验证结果
        $this->assertEquals('测试群', $groupChat->getName());
        $this->assertEquals('群公告内容', $groupChat->getNotice());
        $this->assertInstanceOf(CarbonImmutable::class, $groupChat->getCreateTime());
        $this->assertEquals(1640000000, $groupChat->getCreateTime()->getTimestamp());
    }

    public function testInvokeWithOwnerAndAdmins(): void
    {
        // 准备数据
        $message = new SyncGroupChatDetailMessage();
        $message->setChatId('chat123');

        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);
        
        $groupChat = new GroupChat();
        $groupChat->setChatId('chat123');
        $groupChat->setAgent($agent);
        $groupChat->setCorp($corp);

        $owner = $this->createMock(UserInterface::class);
        $admin1 = $this->createMock(UserInterface::class);
        $admin2 = $this->createMock(UserInterface::class);

        $this->groupChatRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($groupChat);

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn([
                'group_chat' => [
                    'name' => '测试群',
                    'notice' => '',
                    'create_time' => 1640000000,
                    'owner' => 'owner123',
                    'admin_list' => [
                        ['userid' => 'admin1'],
                        ['userid' => 'admin2'],
                        ['userid' => 'admin3'] // 这个找不到
                    ],
                    'member_list' => []
                ]
            ]);

        $this->userLoader->expects($this->exactly(4))
            ->method('loadUserByUserIdAndCorp')
            ->willReturnMap([
                ['owner123', $corp, $owner],
                ['admin1', $corp, $admin1],
                ['admin2', $corp, $admin2],
                ['admin3', $corp, null]
            ]);

        $capturedAdmins = null;
        $capturedMembers = null;
        $this->propertyAccessor->expects($this->exactly(2))
            ->method('setValue')
            ->willReturnCallback(function ($object, $property, $value) use ($groupChat, $admin1, $admin2, &$capturedAdmins, &$capturedMembers) {
                $this->assertSame($groupChat, $object);
                if ($property === 'admins') {
                    $capturedAdmins = $value;
                    $this->assertEquals([$admin1, $admin2], $value);
                } elseif ($property === 'members') {
                    $capturedMembers = $value;
                    $this->assertEmpty($value);
                }
            });

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($groupChat);

        $this->entityManager->expects($this->once())
            ->method('flush');

        // 执行测试
        ($this->handler)($message);

        // 验证结果
        $this->assertSame($owner, $groupChat->getOwner());
    }

    public function testInvokeWithMembers(): void
    {
        // 准备数据
        $message = new SyncGroupChatDetailMessage();
        $message->setChatId('chat123');

        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);
        
        $groupChat = new GroupChat();
        $groupChat->setChatId('chat123');
        $groupChat->setAgent($agent);
        $groupChat->setCorp($corp);

        $this->groupChatRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($groupChat);

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn([
                'group_chat' => [
                    'name' => '测试群',
                    'notice' => '',
                    'create_time' => 1640000000,
                    'owner' => 'owner123',
                    'admin_list' => [],
                    'member_list' => [
                        [
                            'userid' => 'member1',
                            'type' => 1,
                            'join_time' => 1640001000,
                            'join_scene' => 1,
                            'invitor' => ['userid' => 'invitor1'],
                            'group_nickname' => '成员昵称1',
                            'name' => '真实姓名1'
                        ],
                        [
                            'userid' => 'member2',
                            'type' => 2,
                            'join_time' => 1640002000,
                            'join_scene' => 2,
                            'invitor' => ['userid' => 'invitor2'],
                            'group_nickname' => '成员昵称2',
                            'name' => '真实姓名2'
                        ]
                    ]
                ]
            ]);

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->with('owner123', $corp)
            ->willReturn(null);

        $capturedMembers = null;
        $this->propertyAccessor->expects($this->exactly(2))
            ->method('setValue')
            ->willReturnCallback(function ($object, $property, $value) use (&$capturedMembers) {
                if ($property === 'members') {
                    $capturedMembers = $value;
                }
            });

        $this->entityManager->expects($this->once())
            ->method('persist');

        $this->entityManager->expects($this->once())
            ->method('flush');

        // 执行测试
        ($this->handler)($message);

        // 验证成员数据
        $this->assertIsArray($capturedMembers);
        $this->assertCount(2, $capturedMembers);
        
        // 验证第一个成员
        $member1 = $capturedMembers[0];
        $this->assertInstanceOf(GroupMember::class, $member1);
        $this->assertSame($groupChat, $member1->getGroupChat());
        $this->assertEquals('member1', $member1->getUserId());
        $this->assertEquals(1, $member1->getType());
        $this->assertEquals(1640001000, $member1->getJoinTime()->getTimestamp());
        $this->assertEquals(1, $member1->getJoinScene());
        $this->assertEquals('invitor1', $member1->getInvitorUserId());
        $this->assertEquals('成员昵称1', $member1->getGroupNickname());
        $this->assertEquals('真实姓名1', $member1->getName());

        // 验证第二个成员
        $member2 = $capturedMembers[1];
        $this->assertInstanceOf(GroupMember::class, $member2);
        $this->assertSame($groupChat, $member2->getGroupChat());
        $this->assertEquals('member2', $member2->getUserId());
        $this->assertEquals(2, $member2->getType());
        $this->assertEquals(1640002000, $member2->getJoinTime()->getTimestamp());
        $this->assertEquals(2, $member2->getJoinScene());
        $this->assertEquals('invitor2', $member2->getInvitorUserId());
        $this->assertEquals('成员昵称2', $member2->getGroupNickname());
        $this->assertEquals('真实姓名2', $member2->getName());
    }

    public function testInvokeWithCompleteData(): void
    {
        // 准备数据
        $message = new SyncGroupChatDetailMessage();
        $message->setChatId('chat123');

        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);
        
        $groupChat = new GroupChat();
        $groupChat->setChatId('chat123');
        $groupChat->setAgent($agent);
        $groupChat->setCorp($corp);

        $owner = $this->createMock(UserInterface::class);
        $admin = $this->createMock(UserInterface::class);

        $this->groupChatRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($groupChat);

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn([
                'group_chat' => [
                    'name' => '完整测试群',
                    'notice' => '这是群公告',
                    'create_time' => 1640000000,
                    'owner' => 'owner123',
                    'admin_list' => [
                        ['userid' => 'admin123']
                    ],
                    'member_list' => [
                        [
                            'userid' => 'member123',
                            'type' => 1,
                            'join_time' => 1640001000,
                            'join_scene' => 3,
                            'invitor' => ['userid' => 'owner123'],
                            'group_nickname' => '群昵称',
                            'name' => '成员姓名'
                        ]
                    ]
                ]
            ]);

        $this->userLoader->expects($this->exactly(2))
            ->method('loadUserByUserIdAndCorp')
            ->willReturnMap([
                ['owner123', $corp, $owner],
                ['admin123', $corp, $admin]
            ]);

        $this->propertyAccessor->expects($this->exactly(2))
            ->method('setValue');

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($groupChat);

        $this->entityManager->expects($this->once())
            ->method('flush');

        // 执行测试
        ($this->handler)($message);

        // 验证结果
        $this->assertEquals('完整测试群', $groupChat->getName());
        $this->assertEquals('这是群公告', $groupChat->getNotice());
        $this->assertSame($owner, $groupChat->getOwner());
    }
}