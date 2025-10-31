<?php

namespace WechatWorkGroupChatBundle\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use WechatWorkGroupChatBundle\Entity\GroupChat;
use WechatWorkGroupChatBundle\Entity\GroupMember;

class GroupMemberFixtures extends AppFixtures implements DependentFixtureInterface
{
    public const GROUP_MEMBER_REFERENCE_PREFIX = 'group_member_';
    public const MEMBERS_PER_GROUP = 8;

    public function load(ObjectManager $manager): void
    {
        $memberCount = 0;

        for ($i = 0; $i < GroupChatFixtures::GROUP_CHAT_COUNT; ++$i) {
            $groupChat = $this->getReference(GroupChatFixtures::GROUP_CHAT_REFERENCE_PREFIX . $i, GroupChat::class);

            $membersInThisGroup = $this->faker->numberBetween(3, self::MEMBERS_PER_GROUP);

            for ($j = 0; $j < $membersInThisGroup; ++$j) {
                $member = $this->createGroupMember($groupChat);
                $manager->persist($member);
                $this->addReference(self::GROUP_MEMBER_REFERENCE_PREFIX . $memberCount, $member);
                ++$memberCount;
            }
        }

        for ($i = 0; $i < 5; ++$i) {
            $groupChat = $this->getReference(GroupChatFixtures::GROUP_CHAT_REFERENCE_PREFIX . (GroupChatFixtures::GROUP_CHAT_COUNT + $i), GroupChat::class);

            for ($j = 0; $j < 5; ++$j) {
                $member = $this->createGroupMember($groupChat);
                $manager->persist($member);
                $this->addReference(self::GROUP_MEMBER_REFERENCE_PREFIX . $memberCount, $member);
                ++$memberCount;
            }
        }

        $manager->flush();
    }

    private function createGroupMember(GroupChat $groupChat): GroupMember
    {
        $member = new GroupMember();
        $member->setGroupChat($groupChat);
        $member->setUserId($this->generateUserId());
        $member->setType($this->faker->numberBetween(1, 3));

        $joinTime = $this->faker->dateTimeBetween($groupChat->getCreateTime()?->format('Y-m-d H:i:s') ?? 'now', 'now');
        $member->setJoinTime(\DateTimeImmutable::createFromMutable($joinTime));

        $member->setJoinScene($this->faker->numberBetween(1, 5));

        if ($this->faker->boolean(60)) {
            $member->setInvitorUserId($this->generateUserId());
        }

        if ($this->faker->boolean(80)) {
            $member->setGroupNickname($this->faker->name());
        }

        if ($this->faker->boolean(90)) {
            $member->setName($this->faker->name());
        }

        $createTime = $member->getJoinTime();
        $member->setCreateTime($createTime);
        $member->setUpdateTime($createTime);

        return $member;
    }

    public function getDependencies(): array
    {
        return [
            GroupChatFixtures::class,
        ];
    }
}
