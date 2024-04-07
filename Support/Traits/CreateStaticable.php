<?php

namespace Modules\Core\Support\Traits;

trait CreateStaticable
{
    public static function create(...$parameters): static
    {
        return static::new(...$parameters);
    }

    public static function make(...$parameters): static
    {
        return static::new(...$parameters);
    }

    public static function new(...$parameters): static
    {
        return new static(...$parameters);
    }
}
