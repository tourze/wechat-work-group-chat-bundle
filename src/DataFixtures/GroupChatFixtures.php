<?php

namespace WechatWorkGroupChatBundle\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use WechatWorkGroupChatBundle\Entity\GroupChat;
use WechatWorkGroupChatBundle\Enum\GroupChatStatus;

class GroupChatFixtures extends AppFixtures
{
    public const GROUP_CHAT_REFERENCE_PREFIX = 'group_chat_';
    public const GROUP_CHAT_COUNT = 20;

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::GROUP_CHAT_COUNT; ++$i) {
            $groupChat = $this->createGroupChat();
            $manager->persist($groupChat);
            $this->addReference(self::GROUP_CHAT_REFERENCE_PREFIX . $i, $groupChat);
        }

        $this->createSpecialGroupChats($manager);

        $manager->flush();
    }

    private function createGroupChat(): GroupChat
    {
        $groupChat = new GroupChat();
        $groupChat->setChatId($this->generateChatId());
        $groupChat->setName($this->generateGroupName());
        $status = $this->faker->randomElement(GroupChatStatus::cases());
        $groupChat->setStatus($status instanceof GroupChatStatus ? $status : null);

        if ($this->faker->boolean(70)) {
            $groupChat->setNotice($this->generateGroupNotice());
        }

        $createTime = $this->faker->dateTimeBetween('-90 days', '-1 day');
        $groupChat->setCreateTime(\DateTimeImmutable::createFromMutable($createTime));

        return $groupChat;
    }

    private function createSpecialGroupChats(ObjectManager $manager): void
    {
        $specialGroups = [
            ['name' => '全体员工群', 'status' => GroupChatStatus::NORMAL],
            ['name' => '技术交流群', 'status' => GroupChatStatus::NORMAL],
            ['name' => '销售团队群', 'status' => GroupChatStatus::NORMAL],
            ['name' => '客服支持群', 'status' => GroupChatStatus::NORMAL],
            ['name' => '已解散测试群', 'status' => GroupChatStatus::RESIGN],
        ];

        foreach ($specialGroups as $index => $data) {
            $groupChat = new GroupChat();
            $groupChat->setChatId($this->generateChatId());
            $groupChat->setName($data['name']);
            $groupChat->setStatus($data['status']);
            $groupChat->setNotice('这是' . $data['name'] . '的群公告');
            $groupChat->setCreateTime(new \DateTimeImmutable('-30 days'));

            $manager->persist($groupChat);
            $this->addReference(self::GROUP_CHAT_REFERENCE_PREFIX . (self::GROUP_CHAT_COUNT + $index), $groupChat);
        }
    }
}
