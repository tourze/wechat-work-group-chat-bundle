<?php

namespace WechatWorkGroupChatBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\CreateTimeAware;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\CorpInterface;
use Tourze\WechatWorkContracts\UserInterface;
use WechatWorkGroupChatBundle\Enum\GroupChatStatus;
use WechatWorkGroupChatBundle\Repository\GroupChatRepository;

#[ORM\Entity(repositoryClass: GroupChatRepository::class)]
#[ORM\Table(name: 'wechat_work_group_chat', options: ['comment' => '客户群'])]
class GroupChat implements \Stringable
{
    use CreateTimeAware;
    use SnowflakeKeyAware;

    #[ORM\Column(length: 64, options: ['comment' => '客户群ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private ?string $chatId = null;

    #[ORM\Column(nullable: true, enumType: GroupChatStatus::class, options: ['comment' => '跟进状态'])]
    #[Assert\Choice(callback: [GroupChatStatus::class, 'cases'])]
    private ?GroupChatStatus $status = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '群名称'])]
    #[Assert\Length(max: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '群公告'])]
    #[Assert\Length(max: 65535)]
    private ?string $notice = null;

    #[ORM\ManyToOne]
    private ?AgentInterface $agent = null;

    #[ORM\ManyToOne]
    private ?CorpInterface $corp = null;

    #[ORM\ManyToOne]
    private ?UserInterface $owner = null;

    /**
     * @var Collection<int, UserInterface>
     */
    #[ORM\ManyToMany(targetEntity: UserInterface::class, fetch: 'EXTRA_LAZY')]
    private Collection $admins;

    /**
     * @var Collection<int, GroupMember>
     */
    #[ORM\OneToMany(targetEntity: GroupMember::class, mappedBy: 'groupChat', orphanRemoval: true, fetch: 'EXTRA_LAZY')]
    private Collection $members;

    public function __construct()
    {
        $this->admins = new ArrayCollection();
        $this->members = new ArrayCollection();
    }

    public function getChatId(): ?string
    {
        return $this->chatId;
    }

    public function setChatId(string $chatId): void
    {
        $this->chatId = $chatId;
    }

    public function getStatus(): ?GroupChatStatus
    {
        return $this->status;
    }

    public function setStatus(?GroupChatStatus $status): void
    {
        $this->status = $status;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getNotice(): ?string
    {
        return $this->notice;
    }

    public function setNotice(?string $notice): void
    {
        $this->notice = $notice;
    }

    public function getAgent(): ?AgentInterface
    {
        return $this->agent;
    }

    public function setAgent(?AgentInterface $agent): void
    {
        $this->agent = $agent;
    }

    public function getCorp(): ?CorpInterface
    {
        return $this->corp;
    }

    public function setCorp(?CorpInterface $corp): void
    {
        $this->corp = $corp;
    }

    public function getOwner(): ?UserInterface
    {
        return $this->owner;
    }

    public function setOwner(?UserInterface $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return Collection<int, UserInterface>
     */
    public function getAdmins(): Collection
    {
        return $this->admins;
    }

    public function addAdmin(UserInterface $admin): void
    {
        if (!$this->admins->contains($admin)) {
            $this->admins->add($admin);
        }
    }

    public function removeAdmin(UserInterface $admin): void
    {
        $this->admins->removeElement($admin);
    }

    /**
     * @return Collection<int, GroupMember>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(GroupMember $member): void
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
            $member->setGroupChat($this);
        }
    }

    public function removeMember(GroupMember $member): void
    {
        if ($this->members->removeElement($member)) {
            // set the owning side to null (unless already changed)
            if ($member->getGroupChat() === $this) {
                $member->setGroupChat(null);
            }
        }
    }

    public function __toString(): string
    {
        return $this->name ?? $this->chatId ?? '';
    }
}
