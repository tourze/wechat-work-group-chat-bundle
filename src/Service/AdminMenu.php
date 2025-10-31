<?php

declare(strict_types=1);

namespace WechatWorkGroupChatBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use WechatWorkGroupChatBundle\Entity\GroupChat;
use WechatWorkGroupChatBundle\Entity\GroupMember;

/**
 * 企业微信客户群管理后台菜单提供者
 */
#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('第三方集成')) {
            $item->addChild('第三方集成');
        }

        $integrationMenu = $item->getChild('第三方集成');
        if (null === $integrationMenu) {
            return;
        }

        // 添加企业微信管理子菜单
        if (null === $integrationMenu->getChild('企业微信')) {
            $integrationMenu->addChild('企业微信')
                ->setAttribute('icon', 'fab fa-weixin')
            ;
        }

        $wechatMenu = $integrationMenu->getChild('企业微信');
        if (null === $wechatMenu) {
            return;
        }

        // 添加客户群管理子菜单
        if (null === $wechatMenu->getChild('客户群管理')) {
            $wechatMenu->addChild('客户群管理')
                ->setAttribute('icon', 'fas fa-users')
            ;
        }

        $groupChatMenu = $wechatMenu->getChild('客户群管理');
        if (null === $groupChatMenu) {
            return;
        }

        $groupChatMenu->addChild('客户群列表')
            ->setUri($this->linkGenerator->getCurdListPage(GroupChat::class))
            ->setAttribute('icon', 'fas fa-comments')
        ;

        $groupChatMenu->addChild('群成员管理')
            ->setUri($this->linkGenerator->getCurdListPage(GroupMember::class))
            ->setAttribute('icon', 'fas fa-user-friends')
        ;
    }
}
