<?php

namespace WechatWorkGroupChatBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
abstract class AppFixtures extends Fixture
{
    protected Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('zh_CN');
    }

    abstract public function load(ObjectManager $manager): void;

    protected function generateChatId(): string
    {
        return 'wr' . $this->faker->regexify('[a-zA-Z0-9]{20}');
    }

    protected function generateUserId(): string
    {
        $prefix = $this->faker->randomElement(['user', 'staff', 'admin']);
        assert(is_string($prefix));

        return $prefix . $this->faker->numberBetween(1000, 9999);
    }

    protected function generateGroupName(): string
    {
        $prefixes = ['销售', '技术', '客户', '产品', '市场', '运营', '客服'];
        $suffixes = ['团队', '小组', '部门', '讨论组', '工作群'];

        $prefix = $this->faker->randomElement($prefixes);
        $suffix = $this->faker->randomElement($suffixes);
        assert(is_string($prefix));
        assert(is_string($suffix));

        return $prefix . $suffix . $this->faker->numberBetween(1, 20);
    }

    protected function generateGroupNotice(): string
    {
        $notices = [
            '欢迎加入我们的工作群，请大家积极参与讨论！',
            '本群为工作群，请保持专业交流。',
            '群内禁止发布与工作无关的信息。',
            '请大家及时查看和回复重要消息。',
            '工作时间：周一至周五 9:00-18:00',
        ];

        $notice = $this->faker->randomElement($notices);
        assert(is_string($notice));

        return $notice;
    }
}
