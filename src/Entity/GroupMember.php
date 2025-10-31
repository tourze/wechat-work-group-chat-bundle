<?php

namespace WechatWorkGroupChatBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
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
    #[Assert\NotBlank]
    #[Assert\Length(max: 128)]
    private ?string $userId = null;

    #[ORM\Column(nullable: true, options: ['comment' => '成员类型'])]
    #[Assert\Type(type: 'integer')]
    private ?int $type = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '加入时间'])]
    #[Assert\Type(type: '\DateTimeImmutable')]
    private ?\DateTimeImmutable $joinTime = null;

    #[ORM\Column(nullable: true, options: ['comment' => '加入场景'])]
    #[Assert\Type(type: 'integer')]
    private ?int $joinScene = null;

    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '邀请人用户ID'])]
    #[Assert\Length(max: 128)]
    private ?string $invitorUserId = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '群昵称'])]
    #[Assert\Length(max: 100)]
    private ?string $groupNickname = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '名称'])]
    #[Assert\Length(max: 100)]
    private ?string $name = null;

    public function getGroupChat(): ?GroupChat
    {
        return $this->groupChat;
    }

    public function setGroupChat(?GroupChat $groupChat): void
    {
        $this->groupChat = $groupChat;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): void
    {
        $this->type = $type;
    }

    public function getJoinTime(): ?\DateTimeImmutable
    {
        return $this->joinTime;
    }

    public function setJoinTime(?\DateTimeImmutable $joinTime): void
    {
        $this->joinTime = $joinTime;
    }

    public function getJoinScene(): ?int
    {
        return $this->joinScene;
    }

    public function setJoinScene(?int $joinScene): void
    {
        $this->joinScene = $joinScene;
    }

    public function getInvitorUserId(): ?string
    {
        return $this->invitorUserId;
    }

    public function setInvitorUserId(?string $invitorUserId): void
    {
        $this->invitorUserId = $invitorUserId;
    }

    public function getGroupNickname(): ?string
    {
        return $this->groupNickname;
    }

    public function setGroupNickname(?string $groupNickname): void
    {
        $this->groupNickname = $groupNickname;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function __toString(): string
    {
        return $this->name ?? $this->groupNickname ?? $this->userId ?? '';
    }
}
