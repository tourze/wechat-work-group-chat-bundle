<?php

namespace WechatWorkGroupChatBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatWorkGroupChatBundle\Repository\GroupMemberRepository;

#[ORM\Entity(repositoryClass: GroupMemberRepository::class)]
#[ORM\Table(name: 'wechat_work_group_member', options: ['comment' => '客户群成员'])]
class GroupMember implements \Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[ORM\ManyToOne(inversedBy: 'members')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GroupChat $groupChat = null;

    #[ORM\Column(length: 128, options: ['comment' => '用户ID'])]
    private ?string $userId = null;

    #[ORM\Column(nullable: true, options: ['comment' => '成员类型'])]
    private ?int $type = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '加入时间'])]
    private ?\DateTimeImmutable $joinTime = null;

    #[ORM\Column(nullable: true, options: ['comment' => '加入场景'])]
    private ?int $joinScene = null;

    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '邀请人用户ID'])]
    private ?string $invitorUserId = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '群昵称'])]
    private ?string $groupNickname = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '名称'])]
    private ?string $name = null;

    public function getGroupChat(): ?GroupChat
    {
        return $this->groupChat;
    }

    public function setGroupChat(?GroupChat $groupChat): static
    {
        $this->groupChat = $groupChat;

        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getJoinTime(): ?\DateTimeImmutable
    {
        return $this->joinTime;
    }

    public function setJoinTime(?\DateTimeImmutable $joinTime): static
    {
        $this->joinTime = $joinTime;

        return $this;
    }

    public function getJoinScene(): ?int
    {
        return $this->joinScene;
    }

    public function setJoinScene(?int $joinScene): static
    {
        $this->joinScene = $joinScene;

        return $this;
    }

    public function getInvitorUserId(): ?string
    {
        return $this->invitorUserId;
    }

    public function setInvitorUserId(?string $invitorUserId): static
    {
        $this->invitorUserId = $invitorUserId;

        return $this;
    }

    public function getGroupNickname(): ?string
    {
        return $this->groupNickname;
    }

    public function setGroupNickname(?string $groupNickname): static
    {
        $this->groupNickname = $groupNickname;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? $this->groupNickname ?? $this->userId ?? '';
    }
}
