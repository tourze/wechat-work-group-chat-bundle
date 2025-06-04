<?php

namespace WechatWorkGroupChatBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use WechatWorkGroupChatBundle\Enum\GroupChatStatus;
use function WechatWorkExternalContactBundle\Tests\Enum\sort;

/**
 * GroupChatStatus枚举测试
 */
class GroupChatStatusTest extends TestCase
{
    public function test_enumCases_hasCorrectValues(): void
    {
        // 测试枚举值
        $this->assertSame(0, GroupChatStatus::NORMAL->value);
        $this->assertSame(1, GroupChatStatus::RESIGN->value);
        $this->assertSame(2, GroupChatStatus::INHERIT_DOING->value);
        $this->assertSame(3, GroupChatStatus::INHERIT_FINISHED->value);
    }

    public function test_enumCases_hasCorrectCount(): void
    {
        // 测试枚举数量
        $cases = GroupChatStatus::cases();
        $this->assertCount(4, $cases);
    }

    public function test_enumCases_containsExpectedValues(): void
    {
        // 测试所有枚举值
        $cases = GroupChatStatus::cases();
        $values = array_map(fn($case) => $case->value, $cases);
        
        $this->assertContains(0, $values);
        $this->assertContains(1, $values);
        $this->assertContains(2, $values);
        $this->assertContains(3, $values);
    }

    public function test_getLabel_returnCorrectLabels(): void
    {
        // 测试标签方法
        $this->assertSame('跟进人正常', GroupChatStatus::NORMAL->getLabel());
        $this->assertSame('跟进人离职', GroupChatStatus::RESIGN->getLabel());
        $this->assertSame('离职继承中', GroupChatStatus::INHERIT_DOING->getLabel());
        $this->assertSame('离职继承完成', GroupChatStatus::INHERIT_FINISHED->getLabel());
    }

    public function test_getLabel_returnsNonEmptyStrings(): void
    {
        // 测试标签非空
        foreach (GroupChatStatus::cases() as $case) {
            $label = $case->getLabel();
            $this->assertIsString($label);
            $this->assertNotEmpty($label);
        }
    }

    public function test_getLabel_uniqueLabels(): void
    {
        // 测试标签唯一性
        $labels = [];
        foreach (GroupChatStatus::cases() as $case) {
            $labels[] = $case->getLabel();
        }
        
        $this->assertSame(count($labels), count(array_unique($labels)));
    }

    public function test_fromInt_withValidValues(): void
    {
        // 测试整数转换为枚举
        $normal = GroupChatStatus::from(0);
        $resign = GroupChatStatus::from(1);
        $inheritDoing = GroupChatStatus::from(2);
        $inheritFinished = GroupChatStatus::from(3);
        
        $this->assertSame(GroupChatStatus::NORMAL, $normal);
        $this->assertSame(GroupChatStatus::RESIGN, $resign);
        $this->assertSame(GroupChatStatus::INHERIT_DOING, $inheritDoing);
        $this->assertSame(GroupChatStatus::INHERIT_FINISHED, $inheritFinished);
    }

    public function test_fromInt_withInvalidValue_throwsException(): void
    {
        // 测试无效值抛出异常
        $this->expectException(\ValueError::class);
        GroupChatStatus::from(99);
    }

    public function test_fromInt_withNegativeValue_throwsException(): void
    {
        // 测试负数抛出异常
        $this->expectException(\ValueError::class);
        GroupChatStatus::from(-1);
    }

    public function test_tryFromInt_withValidValues(): void
    {
        // 测试tryFrom方法
        $normal = GroupChatStatus::tryFrom(0);
        $resign = GroupChatStatus::tryFrom(1);
        $inheritDoing = GroupChatStatus::tryFrom(2);
        $inheritFinished = GroupChatStatus::tryFrom(3);
        
        $this->assertSame(GroupChatStatus::NORMAL, $normal);
        $this->assertSame(GroupChatStatus::RESIGN, $resign);
        $this->assertSame(GroupChatStatus::INHERIT_DOING, $inheritDoing);
        $this->assertSame(GroupChatStatus::INHERIT_FINISHED, $inheritFinished);
    }

    public function test_tryFromInt_withInvalidValue_returnsNull(): void
    {
        // 测试tryFrom方法返回null
        $result = GroupChatStatus::tryFrom(99);
        $this->assertNull($result);
        
        $result = GroupChatStatus::tryFrom(-1);
        $this->assertNull($result);
    }

    public function test_enumImplementsExpectedInterfaces(): void
    {
        // 测试枚举实现的接口
        $this->assertInstanceOf(\Tourze\EnumExtra\Labelable::class, GroupChatStatus::NORMAL);
        $this->assertInstanceOf(\Tourze\EnumExtra\Itemable::class, GroupChatStatus::NORMAL);
        $this->assertInstanceOf(\Tourze\EnumExtra\Selectable::class, GroupChatStatus::NORMAL);
    }

    public function test_enumUsesExpectedTraits(): void
    {
        // 测试Trait方法存在性
        $this->assertTrue(method_exists(GroupChatStatus::class, 'toArray'));
        $this->assertTrue(method_exists(GroupChatStatus::class, 'genOptions'));
        $this->assertTrue(method_exists(GroupChatStatus::class, 'toSelectItem'));
    }

    public function test_genOptions_returnArray(): void
    {
        // 测试genOptions方法
        $options = GroupChatStatus::genOptions();
        
        $this->assertIsArray($options);
        $this->assertCount(4, $options);
        
        foreach ($options as $option) {
            $this->assertIsArray($option);
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('text', $option);
            $this->assertArrayHasKey('name', $option);
        }
        
        // 验证选项包含预期值
        $values = array_column($options, 'value');
        $this->assertContains(0, $values);
        $this->assertContains(1, $values);
        $this->assertContains(2, $values);
        $this->assertContains(3, $values);
    }

    public function test_toArray_withValidEnum(): void
    {
        // 测试toArray方法
        $array = GroupChatStatus::NORMAL->toArray();
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertSame(0, $array['value']);
        $this->assertSame('跟进人正常', $array['label']);
    }

    public function test_toSelectItem_withValidEnum(): void
    {
        // 测试toSelectItem方法
        $item = GroupChatStatus::RESIGN->toSelectItem();
        
        $this->assertIsArray($item);
        $this->assertArrayHasKey('value', $item);
        $this->assertArrayHasKey('label', $item);
        $this->assertArrayHasKey('text', $item);
        $this->assertArrayHasKey('name', $item);
        $this->assertSame(1, $item['value']);
        $this->assertSame('跟进人离职', $item['label']);
    }

    public function test_businessScenarios_normalStatus(): void
    {
        // 测试业务场景：正常状态
        $normal = GroupChatStatus::NORMAL;
        
        $this->assertSame(0, $normal->value);
        $this->assertSame('跟进人正常', $normal->getLabel());
        $this->assertTrue(str_contains($normal->getLabel(), '正常'));
    }

    public function test_businessScenarios_resignStatus(): void
    {
        // 测试业务场景：离职状态
        $resign = GroupChatStatus::RESIGN;
        
        $this->assertSame(1, $resign->value);
        $this->assertSame('跟进人离职', $resign->getLabel());
        $this->assertTrue(str_contains($resign->getLabel(), '离职'));
    }

    public function test_businessScenarios_inheritanceStatuses(): void
    {
        // 测试业务场景：继承状态
        $inheritDoing = GroupChatStatus::INHERIT_DOING;
        $inheritFinished = GroupChatStatus::INHERIT_FINISHED;
        
        $this->assertSame(2, $inheritDoing->value);
        $this->assertSame('离职继承中', $inheritDoing->getLabel());
        $this->assertTrue(str_contains($inheritDoing->getLabel(), '继承'));
        $this->assertTrue(str_contains($inheritDoing->getLabel(), '中'));
        
        $this->assertSame(3, $inheritFinished->value);
        $this->assertSame('离职继承完成', $inheritFinished->getLabel());
        $this->assertTrue(str_contains($inheritFinished->getLabel(), '继承'));
        $this->assertTrue(str_contains($inheritFinished->getLabel(), '完成'));
    }

    public function test_enumSerialization(): void
    {
        // 测试枚举序列化
        $normal = GroupChatStatus::NORMAL;
        $serialized = serialize($normal);
        $unserialized = unserialize($serialized);
        
        $this->assertSame($normal, $unserialized);
        $this->assertSame($normal->value, $unserialized->value);
        $this->assertSame($normal->getLabel(), $unserialized->getLabel());
    }

    public function test_enumComparison(): void
    {
        // 测试枚举比较
        $normal1 = GroupChatStatus::NORMAL;
        $normal2 = GroupChatStatus::NORMAL;
        $resign = GroupChatStatus::RESIGN;
        
        $this->assertSame($normal1, $normal2);
        $this->assertNotSame($normal1, $resign);
        $this->assertTrue($normal1 === $normal2);
        $this->assertFalse($normal1 === $resign);
    }

    public function test_enumInArrayCheck(): void
    {
        // 测试枚举在数组中的检查
        $statuses = [GroupChatStatus::NORMAL, GroupChatStatus::RESIGN];
        
        $this->assertTrue(in_array(GroupChatStatus::NORMAL, $statuses, true));
        $this->assertTrue(in_array(GroupChatStatus::RESIGN, $statuses, true));
        $this->assertFalse(in_array(GroupChatStatus::INHERIT_DOING, $statuses, true));
    }

    public function test_enumSwitchStatement(): void
    {
        // 测试枚举在switch语句中的使用
        $result1 = match(GroupChatStatus::NORMAL) {
            GroupChatStatus::NORMAL => 'active',
            GroupChatStatus::RESIGN => 'resigned',
            GroupChatStatus::INHERIT_DOING => 'inheriting',
            GroupChatStatus::INHERIT_FINISHED => 'inherited'
        };
        
        $result2 = match(GroupChatStatus::INHERIT_FINISHED) {
            GroupChatStatus::NORMAL => 'active',
            GroupChatStatus::RESIGN => 'resigned',
            GroupChatStatus::INHERIT_DOING => 'inheriting',
            GroupChatStatus::INHERIT_FINISHED => 'inherited'
        };
        
        $this->assertSame('active', $result1);
        $this->assertSame('inherited', $result2);
    }

    public function test_sequentialValues(): void
    {
        // 测试枚举值是连续的
        $values = [
            GroupChatStatus::NORMAL->value,
            GroupChatStatus::RESIGN->value,
            GroupChatStatus::INHERIT_DOING->value,
            GroupChatStatus::INHERIT_FINISHED->value,
        ];
        
        sort($values);
        $expectedValues = [0, 1, 2, 3];
        
        $this->assertSame($expectedValues, $values);
    }
} 