<?php

namespace WechatWorkGroupChatBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatWorkGroupChatBundle\Command\SyncGroupChatListCommand;

/**
 * @internal
 */
#[CoversClass(SyncGroupChatListCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncGroupChatListCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SyncGroupChatListCommand::class);

        return new CommandTester($command);
    }

    public function testCommandExecutesSuccessfully(): void
    {
        $commandTester = $this->getCommandTester();

        $exitCode = $commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $exitCode);
    }

    public function testCommandName(): void
    {
        $this->assertEquals('wechat-work:sync-group-chat-list', SyncGroupChatListCommand::NAME);
    }

    public function testCommandExtendsCommand(): void
    {
        $reflection = new \ReflectionClass(SyncGroupChatListCommand::class);
        $this->assertTrue($reflection->isSubclassOf('Symfony\Component\Console\Command\Command'));
    }

    public function testCommandHasRequiredAttributes(): void
    {
        $reflection = new \ReflectionClass(SyncGroupChatListCommand::class);
        $attributes = $reflection->getAttributes();

        $this->assertNotEmpty($attributes);
        $attributeNames = array_map(fn ($attr) => $attr->getName(), $attributes);

        $this->assertContains('Tourze\Symfony\CronJob\Attribute\AsCronTask', $attributeNames);
        $this->assertContains('Symfony\Component\Console\Attribute\AsCommand', $attributeNames);
    }
}
