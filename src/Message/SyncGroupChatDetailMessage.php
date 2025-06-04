<?php

namespace WechatWorkGroupChatBundle\Message;

use Tourze\AsyncContracts\AsyncMessageInterface;

class SyncGroupChatDetailMessage implements AsyncMessageInterface
{
    /**
     * @var string 唯一ID
     */
    private string $chatId;

    public function getChatId(): string
    {
        return $this->chatId;
    }

    public function setChatId(string $chatId): void
    {
        $this->chatId = $chatId;
    }
}
