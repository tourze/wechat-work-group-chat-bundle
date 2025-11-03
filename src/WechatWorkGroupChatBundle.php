<?php

namespace WechatWorkGroupChatBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use WechatWorkBundle\WechatWorkBundle;
use WechatWorkStaffBundle\WechatWorkStaffBundle;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;

class WechatWorkGroupChatBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            WechatWorkBundle::class => ['all' => true],
            WechatWorkStaffBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
        ];
    }
}
