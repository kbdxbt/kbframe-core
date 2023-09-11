<?php

declare(strict_types=1);

namespace Modules\Core\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait SerializeDate
{
    /**
     * 为数组 / JSON 序列化准备日期。(Laravel 7).
     */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format($this->getDateFormat());
    }
}
