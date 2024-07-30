<?php

namespace Modules\Core\Enums;

use Modules\Core\Support\Traits\EnumConcern;

enum StatusEnum: int
{
    use EnumConcern;

    case ENABLED = 1;
    case DISABLED = 0;

    public function map(): string
    {
        return match ($this) {
            self::ENABLED => '启用',
            self::DISABLED => '禁用',
        };
    }
}
