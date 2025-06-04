<?php

namespace WechatWorkGroupChatBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 获取客户群列表
 *
 * @see https://developer.work.weixin.qq.com/document/path/92120
 */
class GetGroupChatListRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var int|null 客户群跟进状态过滤。
     *               0 - 所有列表(即不过滤)
     *               1 - 离职待继承
     *               2 - 离职继承中
     *               3 - 离职继承完成
     *               默认为0
     */
    private ?int $statusFilter = null;

    /**
     * @var array|null 群主过滤 用户ID列表
     */
    private ?array $ownerUserIds = [];

    /**
     * @var int 分页，预期请求的数据量，取值范围 1 ~ 1000
     */
    private int $limit;

    /**
     * @var string|null 用于分页查询的游标，字符串类型，由上一次调用返回，首次调用不填
     */
    private ?string $cursor = null;

    public function getRequestPath(): string
    {
        return '/cgi-bin/externalcontact/groupchat/list';
    }

    public function getRequestOptions(): ?array
    {
        $json = [
            'limit' => $this->getLimit(),
        ];

        if (null !== $this->getStatusFilter()) {
            $json['status_filter'] = $this->getStatusFilter();
        }
        if (null !== $this->getOwnerUserIds()) {
            $json['owner_filter'] = [
                'userid_list' => $this->getOwnerUserIds(),
            ];
        }
        if (null !== $this->getCursor()) {
            $json['cursor'] = $this->getCursor();
        }

        return [
            'json' => $json,
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }

    public function getStatusFilter(): ?int
    {
        return $this->statusFilter;
    }

    public function setStatusFilter(?int $statusFilter): void
    {
        $this->statusFilter = $statusFilter;
    }

    public function getOwnerUserIds(): ?array
    {
        return $this->ownerUserIds;
    }

    public function setOwnerUserIds(?array $ownerUserIds): void
    {
        $this->ownerUserIds = $ownerUserIds;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getCursor(): ?string
    {
        return $this->cursor;
    }

    public function setCursor(?string $cursor): void
    {
        $this->cursor = $cursor;
    }
}
