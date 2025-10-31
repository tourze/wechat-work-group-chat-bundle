<?php

namespace WechatWorkGroupChatBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use WechatWorkGroupChatBundle\Enum\GroupChatStatus;

/**
 * GroupChatStatus枚举测试
 *
 * @internal
 */
#[CoversClass(GroupChatStatus::class)]
final class GroupChatStatusTest extends AbstractEnumTestCase
{
    public function testEnumCasesHasCorrectValues(): void
    {
        $this->assertSame(0, GroupChatStatus::NORMAL->value);
        $this->assertSame(1, GroupChatStatus::RESIGN->value);
        $this->assertSame(2, GroupChatStatus::INHERIT_DOING->value);
        $this->assertSame(3, GroupChatStatus::INHERIT_FINISHED->value);
    }

    public function testGetLabelReturnCorrectLabels(): void
    {
        $this->assertSame('跟进人正常', GroupChatStatus::NORMAL->getLabel());
        $this->assertSame('跟进人离职', GroupChatStatus::RESIGN->getLabel());
        $this->assertSame('离职继承中', GroupChatStatus::INHERIT_DOING->getLabel());
        $this->assertSame('离职继承完成', GroupChatStatus::INHERIT_FINISHED->getLabel());
    }

    public function testEnumImplementsExpectedInterfaces(): void
    {
        $this->assertInstanceOf(Labelable::class, GroupChatStatus::NORMAL);
        $this->assertInstanceOf(Itemable::class, GroupChatStatus::NORMAL);
        $this->assertInstanceOf(Selectable::class, GroupChatStatus::NORMAL);
    }

    public function testGenOptionsReturnArray(): void
    {
        $options = GroupChatStatus::genOptions();
        $this->assertCount(4, $options);

        foreach ($options as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('text', $option);
            $this->assertArrayHasKey('name', $option);
        }

        $values = array_column($options, 'value');
        $this->assertContains(0, $values);
        $this->assertContains(1, $values);
        $this->assertContains(2, $values);
        $this->assertContains(3, $values);
    }

    public function testToArrayWithValidEnum(): void
    {
        $array = GroupChatStatus::NORMAL->toArray();
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertSame(0, $array['value']);
        $this->assertSame('跟进人正常', $array['label']);
    }

    public function testToSelectItemWithValidEnum(): void
    {
        $item = GroupChatStatus::RESIGN->toSelectItem();
        $this->assertArrayHasKey('value', $item);
        $this->assertArrayHasKey('label', $item);
        $this->assertArrayHasKey('text', $item);
        $this->assertArrayHasKey('name', $item);
        $this->assertSame(1, $item['value']);
        $this->assertSame('跟进人离职', $item['label']);
    }

    public function testBusinessScenariosInheritanceStatuses(): void
    {
        $inheritDoing = GroupChatStatus::INHERIT_DOING;
        $inheritFinished = GroupChatStatus::INHERIT_FINISHED;

        $this->assertSame(2, $inheritDoing->value);
        $this->assertSame('离职继承中', $inheritDoing->getLabel());
        $this->assertStringContainsString('继承', $inheritDoing->getLabel());
        $this->assertStringContainsString('中', $inheritDoing->getLabel());

        $this->assertSame(3, $inheritFinished->value);
        $this->assertSame('离职继承完成', $inheritFinished->getLabel());
        $this->assertStringContainsString('继承', $inheritFinished->getLabel());
        $this->assertStringContainsString('完成', $inheritFinished->getLabel());
    }

    public function testSequentialValues(): void
    {
        $values = [
            GroupChatStatus::NORMAL->value,
            GroupChatStatus::RESIGN->value,
            GroupChatStatus::INHERIT_DOING->value,
            GroupChatStatus::INHERIT_FINISHED->value,
        ];

        \sort($values);
        $expectedValues = [0, 1, 2, 3];

        $this->assertSame($expectedValues, $values);
    }
}
