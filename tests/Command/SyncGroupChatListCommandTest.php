<?php

namespace WechatWorkGroupChatBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Tourze\WechatWorkContracts\UserInterface;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkExternalContactBundle\Enum\GroupChatStatus;
use WechatWorkGroupChatBundle\Command\SyncGroupChatListCommand;
use WechatWorkGroupChatBundle\Entity\GroupChat;
use WechatWorkGroupChatBundle\Message\SyncGroupChatDetailMessage;
use WechatWorkGroupChatBundle\Repository\GroupChatRepository;

class SyncGroupChatListCommandTest extends TestCase
{
    private AgentRepository&MockObject $agentRepository;
    private WorkService&MockObject $workService;
    private UserLoaderInterface&MockObject $userLoader;
    private GroupChatRepository&MockObject $groupChatRepository;
    private MessageBusInterface&MockObject $messageBus;
    private EntityManagerInterface&MockObject $entityManager;
    private SyncGroupChatListCommand $command;

    protected function setUp(): void
    {
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->workService = $this->createMock(WorkService::class);
        $this->userLoader = $this->createMock(UserLoaderInterface::class);
        $this->groupChatRepository = $this->createMock(GroupChatRepository::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->command = new SyncGroupChatListCommand(
            $this->agentRepository,
            $this->workService,
            $this->userLoader,
            $this->groupChatRepository,
            $this->messageBus,
            $this->entityManager
        );
    }

    public function testExecuteWithNoAgents(): void
    {
        // 准备数据
        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $output);

        // 验证结果
        $this->assertEquals(0, $result);
    }

    public function testExecuteWithAgentButNoFollowUsers(): void
    {
        // 准备数据
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn([]); // 没有 follow_user 字段

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $output);

        // 验证结果
        $this->assertEquals(0, $result);
    }

    public function testExecuteWithFollowUserButUserNotFound(): void
    {
        // 准备数据
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn(['follow_user' => ['user123']]);

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->with('user123', $corp)
            ->willReturn(null);

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $output);

        // 验证结果
        $this->assertEquals(0, $result);
    }

    public function testExecuteWithSingleGroupChatCreation(): void
    {
        // 准备数据
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $user = $this->createMock(UserInterface::class);
        $user->method('getUserId')->willReturn('user123');

        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $this->workService->expects($this->exactly(2))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                ['follow_user' => ['user123']], // GetFollowUserListRequest
                [
                    'group_chat_list' => [
                        [
                            'chat_id' => 'chat123',
                            'status' => 0
                        ]
                    ]
                ] // GetGroupChatListRequest
            );

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->with('user123', $corp)
            ->willReturn($user);

        $this->groupChatRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['chatId' => 'chat123'])
            ->willReturn(null);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(GroupChat::class));

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(SyncGroupChatDetailMessage::class))
            ->willReturn(new Envelope(new SyncGroupChatDetailMessage()));

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $output);

        // 验证结果
        $this->assertEquals(0, $result);
    }

    public function testExecuteWithExistingGroupChatUpdate(): void
    {
        // 准备数据
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $user = $this->createMock(UserInterface::class);
        $user->method('getUserId')->willReturn('user123');

        $existingGroupChat = new GroupChat();
        $existingGroupChat->setChatId('chat123');

        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $this->workService->expects($this->exactly(2))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                ['follow_user' => ['user123']],
                [
                    'group_chat_list' => [
                        [
                            'chat_id' => 'chat123',
                            'status' => 0
                        ]
                    ]
                ]
            );

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->willReturn($user);

        $this->groupChatRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['chatId' => 'chat123'])
            ->willReturn($existingGroupChat);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($existingGroupChat);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(SyncGroupChatDetailMessage::class))
            ->willReturn(new Envelope(new SyncGroupChatDetailMessage()));

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $output);

        // 验证结果
        $this->assertEquals(0, $result);
        $this->assertEquals(GroupChatStatus::NORMAL, $existingGroupChat->getStatus());
        $this->assertEquals($agent, $existingGroupChat->getAgent());
        $this->assertEquals($corp, $existingGroupChat->getCorp());
    }

    public function testExecuteWithPagination(): void
    {
        // 准备数据
        $corp = new Corp();
        $agent = new Agent();
        $agent->setCorp($corp);

        $user = $this->createMock(UserInterface::class);
        $user->method('getUserId')->willReturn('user123');

        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent]);

        $this->workService->expects($this->exactly(3))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                ['follow_user' => ['user123']], // GetFollowUserListRequest
                [
                    'next_cursor' => 'cursor123',
                    'group_chat_list' => [
                        [
                            'chat_id' => 'chat1',
                            'status' => 0
                        ]
                    ]
                ], // 第一页 GetGroupChatListRequest
                [
                    'group_chat_list' => [
                        [
                            'chat_id' => 'chat2',
                            'status' => 1
                        ]
                    ]
                ] // 第二页 GetGroupChatListRequest (没有 next_cursor)
            );

        $this->userLoader->expects($this->once())
            ->method('loadUserByUserIdAndCorp')
            ->willReturn($user);

        $this->groupChatRepository->expects($this->exactly(2))
            ->method('findOneBy')
            ->willReturn(null);

        $this->entityManager->expects($this->exactly(2))
            ->method('persist');

        $this->entityManager->expects($this->exactly(2))
            ->method('flush');

        $this->messageBus->expects($this->exactly(2))
            ->method('dispatch')
            ->willReturn(new Envelope(new SyncGroupChatDetailMessage()));

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $output);

        // 验证结果
        $this->assertEquals(0, $result);
    }

    public function testExecuteWithMultipleUsersAndAgents(): void
    {
        // 准备数据
        $corp1 = new Corp();
        $corp2 = new Corp();
        $agent1 = new Agent();
        $agent1->setCorp($corp1);
        $agent2 = new Agent();
        $agent2->setCorp($corp2);

        $user1 = $this->createMock(UserInterface::class);
        $user1->method('getUserId')->willReturn('user1');
        $user2 = $this->createMock(UserInterface::class);
        $user2->method('getUserId')->willReturn('user2');

        $this->agentRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$agent1, $agent2]);

        $this->workService->expects($this->exactly(5))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                ['follow_user' => ['user1', 'user2']], // Agent1 GetFollowUserListRequest
                ['group_chat_list' => [['chat_id' => 'chat1', 'status' => 0]]], // User1 GetGroupChatListRequest
                ['group_chat_list' => [['chat_id' => 'chat2', 'status' => 1]]], // User2 GetGroupChatListRequest
                ['follow_user' => ['user2']], // Agent2 GetFollowUserListRequest
                ['group_chat_list' => [['chat_id' => 'chat3', 'status' => 0]]] // User2 for Agent2 GetGroupChatListRequest
            );

        $this->userLoader->expects($this->exactly(3))
            ->method('loadUserByUserIdAndCorp')
            ->willReturnMap([
                ['user1', $corp1, $user1],
                ['user2', $corp1, $user2],
                ['user2', $corp2, $user2]
            ]);

        $this->groupChatRepository->expects($this->exactly(3))
            ->method('findOneBy')
            ->willReturn(null);

        $this->entityManager->expects($this->exactly(3))
            ->method('persist');

        $this->entityManager->expects($this->exactly(3))
            ->method('flush');

        $this->messageBus->expects($this->exactly(3))
            ->method('dispatch')
            ->willReturn(new Envelope(new SyncGroupChatDetailMessage()));

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        // 执行测试
        $reflection = new \ReflectionClass($this->command);
        $method = $reflection->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($this->command, $input, $output);

        // 验证结果
        $this->assertEquals(0, $result);
    }

    public function testCommandMetadata(): void
    {
        // 验证命令名称
        $this->assertEquals('wechat-work:SyncGroupChatListCommand', $this->command->getName());
        
        // 验证命令描述
        $this->assertEquals('同步客户群数据到本地', $this->command->getDescription());
        
        // 验证构造函数依赖
        $reflection = new \ReflectionClass($this->command);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();
        
        $this->assertCount(6, $parameters);
        $this->assertEquals('agentRepository', $parameters[0]->getName());
        $this->assertEquals('workService', $parameters[1]->getName());
        $this->assertEquals('userLoader', $parameters[2]->getName());
        $this->assertEquals('groupChatRepository', $parameters[3]->getName());
        $this->assertEquals('messageBus', $parameters[4]->getName());
        $this->assertEquals('entityManager', $parameters[5]->getName());
    }
} 