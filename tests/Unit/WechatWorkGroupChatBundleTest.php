<?php

namespace WechatWorkGroupChatBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WechatWorkGroupChatBundle\WechatWorkGroupChatBundle;

class WechatWorkGroupChatBundleTest extends TestCase
{
    public function testInstance(): void
    {
        $bundle = new WechatWorkGroupChatBundle();
        $this->assertInstanceOf(WechatWorkGroupChatBundle::class, $bundle);
    }
}