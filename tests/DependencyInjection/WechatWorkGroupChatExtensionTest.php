<?php

namespace WechatWorkGroupChatBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WechatWorkGroupChatBundle\DependencyInjection\WechatWorkGroupChatExtension;

class WechatWorkGroupChatExtensionTest extends TestCase
{
    private WechatWorkGroupChatExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new WechatWorkGroupChatExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoadServicesConfiguration(): void
    {
        // 执行加载
        $this->extension->load([], $this->container);

        // 验证服务是否已加载（使用完整的类名作为服务ID）
        $this->assertTrue($this->container->hasDefinition('WechatWorkGroupChatBundle\Command\SyncGroupChatListCommand'));
        $this->assertTrue($this->container->hasDefinition('WechatWorkGroupChatBundle\MessageHandler\SyncGroupChatDetailHandler'));
        $this->assertTrue($this->container->hasDefinition('WechatWorkGroupChatBundle\Repository\GroupChatRepository'));
        $this->assertTrue($this->container->hasDefinition('WechatWorkGroupChatBundle\Repository\GroupMemberRepository'));
    }

    public function testServiceTagsAreCorrect(): void
    {
        // 执行加载
        $this->extension->load([], $this->container);

        // 验证命令服务是否配置了自动配置（会自动添加标签）
        $commandDefinition = $this->container->getDefinition('WechatWorkGroupChatBundle\Command\SyncGroupChatListCommand');
        $this->assertTrue($commandDefinition->isAutoconfigured());

        // 验证消息处理器服务是否配置了自动配置（会自动添加标签）
        $handlerDefinition = $this->container->getDefinition('WechatWorkGroupChatBundle\MessageHandler\SyncGroupChatDetailHandler');
        $this->assertTrue($handlerDefinition->isAutoconfigured());
    }

    public function testServicesAreAutowiredAndAutoconfigured(): void
    {
        // 执行加载
        $this->extension->load([], $this->container);

        // 获取服务定义
        $commandDefinition = $this->container->getDefinition('WechatWorkGroupChatBundle\Command\SyncGroupChatListCommand');
        $handlerDefinition = $this->container->getDefinition('WechatWorkGroupChatBundle\MessageHandler\SyncGroupChatDetailHandler');
        $groupChatRepoDefinition = $this->container->getDefinition('WechatWorkGroupChatBundle\Repository\GroupChatRepository');
        $groupMemberRepoDefinition = $this->container->getDefinition('WechatWorkGroupChatBundle\Repository\GroupMemberRepository');

        // 验证自动配置
        $this->assertTrue($commandDefinition->isAutowired());
        $this->assertTrue($commandDefinition->isAutoconfigured());
        $this->assertTrue($handlerDefinition->isAutowired());
        $this->assertTrue($handlerDefinition->isAutoconfigured());
        $this->assertTrue($groupChatRepoDefinition->isAutowired());
        $this->assertTrue($groupChatRepoDefinition->isAutoconfigured());
        $this->assertTrue($groupMemberRepoDefinition->isAutowired());
        $this->assertTrue($groupMemberRepoDefinition->isAutoconfigured());
    }

    public function testLoadWithEmptyConfig(): void
    {
        // 使用空配置加载
        $this->extension->load([], $this->container);

        // 验证容器状态
        $this->assertGreaterThan(0, count($this->container->getDefinitions()));
    }

    public function testLoadWithMultipleConfigArrays(): void
    {
        // 使用多个配置数组加载
        $configs = [
            [],
            [],
            []
        ];
        
        $this->extension->load($configs, $this->container);

        // 验证容器状态
        $this->assertGreaterThan(0, count($this->container->getDefinitions()));
    }
}