<?php

namespace WechatWorkGroupChatBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum GroupChatStatus: int implements Labelable, Itemable, Selectable
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
}
