<?php

namespace WechatWorkGroupChatBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use WechatWorkGroupChatBundle\Repository\GroupMemberRepository;

#[AsPermission(title: '客户群成员')]
#[ORM\Entity(repositoryClass: GroupMemberRepository::class)]
#[ORM\Table(name: 'wechat_work_group_member', options: ['comment' => '客户群成员'])]
class GroupMember
{
    use TimestampableAware;
    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'members')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GroupChat $groupChat = null;

    #[ORM\Column(length: 128)]
    private ?string $userId = null;

    #[ORM\Column(nullable: true)]
    private ?int $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $joinTime = null;

    #[ORM\Column(nullable: true)]
    private ?int $joinScene = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $invitorUserId = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $groupNickname = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $name = null;

    public function getId(): ?string
    {
        return $this->id;
    }

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

    public function getJoinTime(): ?\DateTimeInterface
    {
        return $this->joinTime;
    }

    public function setJoinTime(?\DateTimeInterface $joinTime): static
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
    }}
