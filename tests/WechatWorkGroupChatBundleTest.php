<?php

declare(strict_types=1);

namespace WechatWorkGroupChatBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use WechatWorkGroupChatBundle\WechatWorkGroupChatBundle;

/**
 * @internal
 * @phpstan-ignore symplify.forbiddenExtendOfNonAbstractClass
 */
#[CoversClass(WechatWorkGroupChatBundle::class)]
#[RunTestsInSeparateProcesses]
final class WechatWorkGroupChatBundleTest extends AbstractBundleTestCase
{
}
