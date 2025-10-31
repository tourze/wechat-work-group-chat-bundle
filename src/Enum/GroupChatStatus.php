<?php

namespace WechatWorkGroupChatBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum GroupChatStatus: int implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;
    case NORMAL = 0;
    case RESIGN = 1;
    case INHERIT_DOING = 2;
    case INHERIT_FINISHED = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::NORMAL => '跟进人正常',
            self::RESIGN => '跟进人离职',
            self::INHERIT_DOING => '离职继承中',
            self::INHERIT_FINISHED => '离职继承完成',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::NORMAL => 'success',
            self::RESIGN => 'warning',
            self::INHERIT_DOING => 'info',
            self::INHERIT_FINISHED => 'primary',
        };
    }
}
