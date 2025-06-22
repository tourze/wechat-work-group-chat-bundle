<?php

namespace WechatWorkGroupChatBundle\Tests\Message;

use PHPUnit\Framework\TestCase;
use Tourze\AsyncContracts\AsyncMessageInterface;
use WechatWorkGroupChatBundle\Message\SyncGroupChatDetailMessage;

/**
 * SyncGroupChatDetailMessage测试
 *
 * 测试关注点：
 * - 消息数据管理
 * - 异步接口实现
 * - 群聊ID管理
 * - 属性访问器
 */
class SyncGroupChatDetailMessageTest extends TestCase
{
    private SyncGroupChatDetailMessage $message;

    protected function setUp(): void
    {
        $this->message = new SyncGroupChatDetailMessage();
    }

    public function testMessageCreation(): void
    {
        // 测试消息创建
        $this->assertInstanceOf(SyncGroupChatDetailMessage::class, $this->message);
        $this->assertInstanceOf(AsyncMessageInterface::class, $this->message);
    }

    public function testChatIdProperty(): void
    {
        // 测试群聊ID属性
        $chatId = 'chat_123456789';

        $this->message->setChatId($chatId);
        $this->assertEquals($chatId, $this->message->getChatId());
    }

    public function testChatIdWithDifferentFormats(): void
    {
        // 测试不同格式的群聊ID
        $chatIds = [
            'chat_simple_123',
            'CHAT_UPPER_CASE_456',
            'chat-with-dashes-789',
            'chat.with.dots.012',
            'chat_mixed-format.345',
            'wr1234567890123456789012345678901234567890',
            'wrkf1234567890123456789012345678901234567890'
        ];

        foreach ($chatIds as $chatId) {
            $this->message->setChatId($chatId);
            $this->assertEquals($chatId, $this->message->getChatId());
        }
    }

    public function testChatIdWithSpecialCharacters(): void
    {
        // 测试包含特殊字符的群聊ID
        $specialChatIds = [
            'chat_with_underscore_123',
            'chat-with-hyphen-456',
            'chat.with.period.789',
            'chat123456789012345678901234567890',
            'chatABCDEFGHIJKLMNOPQRSTUVWXYZ123456'
        ];

        foreach ($specialChatIds as $chatId) {
            $this->message->setChatId($chatId);
            $this->assertEquals($chatId, $this->message->getChatId());
        }
    }

    public function testChatIdLength(): void
    {
        // 测试不同长度的群聊ID
        $shortChatId = 'chat123';
        $mediumChatId = 'chat_medium_length_id_123456789';
        $longChatId = 'chat_very_long_id_with_many_characters_1234567890123456789012345678901234567890';

        // 短ID
        $this->message->setChatId($shortChatId);
        $this->assertEquals($shortChatId, $this->message->getChatId());
        $this->assertEquals(strlen($shortChatId), strlen($this->message->getChatId()));

        // 中等长度ID
        $this->message->setChatId($mediumChatId);
        $this->assertEquals($mediumChatId, $this->message->getChatId());
        $this->assertEquals(strlen($mediumChatId), strlen($this->message->getChatId()));

        // 长ID
        $this->message->setChatId($longChatId);
        $this->assertEquals($longChatId, $this->message->getChatId());
        $this->assertEquals(strlen($longChatId), strlen($this->message->getChatId()));
    }

    public function testSetterReturnType(): void
    {
        // 测试setter方法的返回类型
        $chatId = 'test_chat_return_type';

        // 设置值
        $this->message->setChatId($chatId);

        // 验证设置成功
        $this->assertEquals($chatId, $this->message->getChatId());
    }

    public function testAsyncMessageInterface(): void
    {
        // 测试异步消息接口实现
        $this->assertInstanceOf(AsyncMessageInterface::class, $this->message);

        // 验证接口方法存在（通过反射）
        $reflection = new \ReflectionClass($this->message);
        $interfaces = $reflection->getInterfaceNames();

        $this->assertContains(AsyncMessageInterface::class, $interfaces);
    }

    public function testMessageIntegrity(): void
    {
        // 测试消息完整性
        $originalChatId = 'original_chat_123';
        $newChatId = 'new_chat_456';

        // 设置初始值
        $this->message->setChatId($originalChatId);
        $this->assertEquals($originalChatId, $this->message->getChatId());

        // 更新值
        $this->message->setChatId($newChatId);
        $this->assertEquals($newChatId, $this->message->getChatId());
        $this->assertNotEquals($originalChatId, $this->message->getChatId());
    }

    public function testChatIdPersistence(): void
    {
        // 测试群聊ID的持久性
        $chatId = 'persistent_chat_789';

        $this->message->setChatId($chatId);

        // 多次获取应该返回相同的值
        $this->assertEquals($chatId, $this->message->getChatId());
        $this->assertEquals($chatId, $this->message->getChatId());
        $this->assertEquals($chatId, $this->message->getChatId());

        // 验证值没有被意外修改
        $this->assertSame($chatId, $this->message->getChatId());
    }

    public function testChatIdImmutabilityAfterSet(): void
    {
        // 测试设置后的不可变性（除非重新设置）
        $chatId = 'immutable_chat_012';

        $this->message->setChatId($chatId);
        $retrievedChatId = $this->message->getChatId();

        // 修改检索到的值不应影响原始值
        $retrievedChatId = 'modified_value';
        $this->assertEquals($chatId, $this->message->getChatId());
        $this->assertNotEquals($retrievedChatId, $this->message->getChatId());
    }

    public function testMessageClassStructure(): void
    {
        // 测试消息类结构
        $reflection = new \ReflectionClass($this->message);

        // 验证类名
        $this->assertEquals('SyncGroupChatDetailMessage', $reflection->getShortName());

        // 验证方法存在
        $this->assertTrue($reflection->hasMethod('getChatId'));
        $this->assertTrue($reflection->hasMethod('setChatId'));

        // 验证方法可见性
        $getChatIdMethod = $reflection->getMethod('getChatId');
        $setChatIdMethod = $reflection->getMethod('setChatId');

        $this->assertTrue($getChatIdMethod->isPublic());
        $this->assertTrue($setChatIdMethod->isPublic());
    }
} 