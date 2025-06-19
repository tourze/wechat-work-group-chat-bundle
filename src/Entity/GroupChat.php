<?php

namespace WechatWorkGroupChatBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\CorpInterface;
use Tourze\WechatWorkContracts\UserInterface;
use WechatWorkGroupChatBundle\Enum\GroupChatStatus;
use WechatWorkGroupChatBundle\Repository\GroupChatRepository;

#[ORM\Entity(repositoryClass: GroupChatRepository::class)]
#[ORM\Table(name: 'wechat_work_group_chat', options: ['comment' => '客户群'])]
class GroupChat implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[ORM\Column(length: 64, options: ['comment' => '客户群ID'])]
    private ?string $chatId = null;

    #[ORM\Column(nullable: true, enumType: GroupChatStatus::class, options: ['comment' => '跟进状态'])]
    private ?GroupChatStatus $status = null;

    #[IndexColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeImmutable $createTime = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '群名称'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '群公告'])]
    private ?string $notice = null;

    #[ORM\ManyToOne]
    private ?AgentInterface $agent = null;

    #[ORM\ManyToOne]
    private ?CorpInterface $corp = null;

    #[ORM\ManyToOne]
    private ?UserInterface $owner = null;

    #[ORM\ManyToMany(targetEntity: UserInterface::class, fetch: 'EXTRA_LAZY')]
    private Collection $admins;

    #[ORM\OneToMany(targetEntity: GroupMember::class, mappedBy: 'groupChat', orphanRemoval: true)]
    private Collection $members;

    public function __construct()
    {
        $this->admins = new ArrayCollection();
        $this->members = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getChatId(): ?string
    {
        return $this->chatId;
    }

    public function setChatId(string $chatId): static
    {
        $this->chatId = $chatId;

        return $this;
    }

    public function getStatus(): ?GroupChatStatus
    {
        return $this->status;
    }

    public function setStatus(?GroupChatStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function setCreateTime(?\DateTimeImmutable $createdAt): self
    {
        $this->createTime = $createdAt instanceof \DateTimeImmutable ? $createdAt : ($createdAt ? \DateTimeImmutable::createFromInterface($createdAt) : null);

        return $this;
    }

    public function getCreateTime(): ?\DateTimeImmutable
    {
        return $this->createTime;
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

    public function getNotice(): ?string
    {
        return $this->notice;
    }

    public function setNotice(?string $notice): static
    {
        $this->notice = $notice;

        return $this;
    }

    public function getAgent(): ?AgentInterface
    {
        return $this->agent;
    }

    public function setAgent(?AgentInterface $agent): static
    {
        $this->agent = $agent;

        return $this;
    }

    public function getCorp(): ?CorpInterface
    {
        return $this->corp;
    }

    public function setCorp(?CorpInterface $corp): static
    {
        $this->corp = $corp;

        return $this;
    }

    public function getOwner(): ?UserInterface
    {
        return $this->owner;
    }

    public function setOwner(?UserInterface $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, UserInterface>
     */
    public function getAdmins(): Collection
    {
        return $this->admins;
    }

    public function addAdmin(UserInterface $admin): static
    {
        if (!$this->admins->contains($admin)) {
            $this->admins->add($admin);
        }

        return $this;
    }

    public function removeAdmin(UserInterface $admin): static
    {
        $this->admins->removeElement($admin);

        return $this;
    }

    /**
     * @return Collection<int, GroupMember>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(GroupMember $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
            $member->setGroupChat($this);
        }

        return $this;
    }

    public function removeMember(GroupMember $member): static
    {
        if ($this->members->removeElement($member)) {
            // set the owning side to null (unless already changed)
            if ($member->getGroupChat() === $this) {
                $member->setGroupChat(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? $this->chatId ?? '';
    }
}
