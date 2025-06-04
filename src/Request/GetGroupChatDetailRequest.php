<?php

namespace WechatWorkGroupChatBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 获取客户群详情
 *
 * @see https://developer.work.weixin.qq.com/document/path/92122
 */
class GetGroupChatDetailRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string 客户群ID
     */
    private string $chatId;

    /**
     * @var bool 是否需要返回群成员的名字group_chat.member_list.name。0-不返回；1-返回。默认不返回
     */
    private bool $needName = false;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/groupchat/get';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'chat_id' => $this->getChatId(),
                'need_name' => $this->isNeedName() ? 1 : 0,
            ],
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }

    public function getChatId(): string
    {
        return $this->chatId;
    }

    public function setChatId(string $chatId): void
    {
        $this->chatId = $chatId;
    }

    public function isNeedName(): bool
    {
        return $this->needName;
    }

    public function setNeedName(bool $needName): void
    {
        $this->needName = $needName;
    }
}
