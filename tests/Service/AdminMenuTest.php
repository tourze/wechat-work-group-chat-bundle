<?php

declare(strict_types=1);

namespace WechatWorkGroupChatBundle\Tests\Service;

use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use WechatWorkGroupChatBundle\Service\AdminMenu;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private AdminMenu $adminMenu;

    protected function onSetUp(): void
    {
        $this->adminMenu = self::getService(AdminMenu::class);
    }

    public function testInvokeCreatesThirdPartyIntegrationMenu(): void
    {
        $factory = new MenuFactory();
        $rootMenu = $factory->createItem('root');

        ($this->adminMenu)($rootMenu);

        $integrationMenu = $rootMenu->getChild('第三方集成');
        $this->assertNotNull($integrationMenu);
        $this->assertInstanceOf(MenuItem::class, $integrationMenu);
        $this->assertNotNull($integrationMenu->getChild('企业微信'));

        $wechatMenu = $integrationMenu->getChild('企业微信');
        $this->assertInstanceOf(MenuItem::class, $wechatMenu);
        $this->assertSame('fab fa-weixin', $wechatMenu->getAttribute('icon'));

        // 验证客户群管理子菜单
        $this->assertNotNull($wechatMenu->getChild('客户群管理'));

        $groupChatManageMenu = $wechatMenu->getChild('客户群管理');
        $this->assertInstanceOf(MenuItem::class, $groupChatManageMenu);
        $this->assertSame('fas fa-users', $groupChatManageMenu->getAttribute('icon'));

        // 验证子菜单项
        $this->assertNotNull($groupChatManageMenu->getChild('客户群列表'));
        $this->assertNotNull($groupChatManageMenu->getChild('群成员管理'));

        $groupChatMenu = $groupChatManageMenu->getChild('客户群列表');
        $this->assertInstanceOf(MenuItem::class, $groupChatMenu);
        $groupChatUri = $groupChatMenu->getUri();
        $this->assertNotNull($groupChatUri);
        $this->assertStringContainsString('WechatWorkGroupChatBundle%5CEntity%5CGroupChat', $groupChatUri);
        $this->assertSame('fas fa-comments', $groupChatMenu->getAttribute('icon'));

        $groupMemberMenu = $groupChatManageMenu->getChild('群成员管理');
        $this->assertInstanceOf(MenuItem::class, $groupMemberMenu);
        $groupMemberUri = $groupMemberMenu->getUri();
        $this->assertNotNull($groupMemberUri);
        $this->assertStringContainsString('WechatWorkGroupChatBundle%5CEntity%5CGroupMember', $groupMemberUri);
        $this->assertSame('fas fa-user-friends', $groupMemberMenu->getAttribute('icon'));
    }

    public function testInvokeWithExistingThirdPartyIntegrationMenu(): void
    {
        $factory = new MenuFactory();
        $rootMenu = $factory->createItem('root');
        $rootMenu->addChild('第三方集成');

        ($this->adminMenu)($rootMenu);

        $integrationMenu = $rootMenu->getChild('第三方集成');
        $this->assertNotNull($integrationMenu);
        $this->assertNotNull($integrationMenu->getChild('企业微信'));
    }

    public function testInvokeWithExistingWechatMenu(): void
    {
        $factory = new MenuFactory();
        $rootMenu = $factory->createItem('root');
        $integrationMenu = $rootMenu->addChild('第三方集成');
        $integrationMenu->addChild('企业微信');

        ($this->adminMenu)($rootMenu);

        $wechatMenu = $integrationMenu->getChild('企业微信');
        $this->assertNotNull($wechatMenu);
        $this->assertNotNull($wechatMenu->getChild('客户群管理'));
    }

    public function testInvokeWithExistingGroupChatManageMenu(): void
    {
        $factory = new MenuFactory();
        $rootMenu = $factory->createItem('root');
        $integrationMenu = $rootMenu->addChild('第三方集成');
        $wechatMenu = $integrationMenu->addChild('企业微信');
        $wechatMenu->addChild('客户群管理');

        ($this->adminMenu)($rootMenu);

        $wechatMenuResult = $integrationMenu->getChild('企业微信');
        $this->assertNotNull($wechatMenuResult);

        $groupChatManageMenu = $wechatMenuResult->getChild('客户群管理');
        $this->assertNotNull($groupChatManageMenu);
        $this->assertNotNull($groupChatManageMenu->getChild('客户群列表'));
        $this->assertNotNull($groupChatManageMenu->getChild('群成员管理'));
    }

    public function testInvokeHandlesNullThirdPartyIntegrationMenu(): void
    {
        $rootItem = $this->createMock(MenuItem::class);
        $rootItem->method('getChild')
            ->with('第三方集成')
            ->willReturn(null)
        ;

        $rootItem->expects($this->once())
            ->method('addChild')
            ->with('第三方集成')
            ->willReturn($rootItem)
        ;

        // 确保当 getChild 返回 null 时，方法能正常处理
        ($this->adminMenu)($rootItem);

        // Mock对象的expects()已经验证了方法调用次数和参数，无需额外断言
    }

    public function testInvokeHandlesNullWechatMenu(): void
    {
        $integrationItem = $this->createMock(MenuItem::class);
        $integrationItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('企业微信')
            ->willReturn(null)
        ;

        $integrationItem->expects($this->once())
            ->method('addChild')
            ->with('企业微信')
        ;

        $rootItem = $this->createMock(MenuItem::class);
        $rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('第三方集成')
            ->willReturn($integrationItem)
        ;

        // 确保当企业微信菜单返回 null 时，方法能正常处理
        ($this->adminMenu)($rootItem);

        // Mock对象的expects()已经验证了方法调用次数和参数，无需额外断言
    }

    public function testInvokeHandlesNullGroupChatManageMenu(): void
    {
        $wechatItem = $this->createMock(MenuItem::class);
        $wechatItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('客户群管理')
            ->willReturn(null)
        ;

        $wechatItem->expects($this->once())
            ->method('addChild')
            ->with('客户群管理')
        ;

        $integrationItem = $this->createMock(MenuItem::class);
        $integrationItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('企业微信')
            ->willReturn($wechatItem)
        ;

        $rootItem = $this->createMock(MenuItem::class);
        $rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('第三方集成')
            ->willReturn($integrationItem)
        ;

        // 确保当客户群管理菜单返回 null 时，方法能正常处理
        ($this->adminMenu)($rootItem);

        // Mock对象的expects()已经验证了方法调用次数和参数，无需额外断言
    }
}
