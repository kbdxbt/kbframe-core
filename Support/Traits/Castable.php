<?php

namespace Modules\Core\Support\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

trait Castable
{
    protected array $casts = [];

    /**
     * Get the casts array.
     */
    public function getCasts(): array
    {
        return $this->casts;
    }

    public function asJson($value): bool|string
    {
        return json_encode($value);
    }

    public function fromJson($value, $asObject = false)
    {
        return json_decode($value, $asObject);
    }

    protected function cast(array &$source): void
    {
        foreach ($source as $key => &$value) {
            $value = $this->castValue($key, $value);
        }
    }

    protected function castValue(string $key, $value)
    {
        $castType = $this->getCastType($key);

        switch ($castType) {
            case 'int':
            case 'integer':
                return (int) $value;

            case 'float':
            case 'double':
                return (float) $value;

            case 'string':
                return (string) $value;

            case 'bool':
            case 'boolean':
                return (bool) $value;

            case 'object':
                return $this->fromJson($value);

            case 'array':
                return $this->fromJson($value, true);

            case 'json':
                return $this->asJson($value);

            case 'date':
                return $this->asDateTime($value)->toDateString();

            case 'datetime':
                return $this->asDateTime($value)->toDateTimeString();

            case 'timestamp':
                return $this->asDateTime($value)->getTimestamp();
        }

        $castMethod = $this->castMethodName($castType);

        if ($value === null && method_exists($this, $castMethod)) {
            return null;
        }

        return $this->{$castMethod}($value);
    }

    protected function getCastType(string $key): string
    {
        return $this->getCasts()[$key] ?? 'default';
    }

    protected function castMethodName(string $key): string
    {
        return (string) Str::of($key)->start('castTo_')->camel();
    }

    protected function castToDefault($value)
    {
        return $value;
    }

    protected function asDateTime($value): Carbon
    {
        return Date::parse($value);
    }
}
