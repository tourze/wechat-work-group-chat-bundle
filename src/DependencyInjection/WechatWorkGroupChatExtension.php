<?php

namespace WechatWorkGroupChatBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

final class WechatWorkGroupChatExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
