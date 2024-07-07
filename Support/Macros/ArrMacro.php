<?php

declare(strict_types=1);

namespace Modules\Core\Support\Macros;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * @mixin \Illuminate\Support\Arr
 */
class ArrMacro
{
    public function filterFilled(): callable
    {
        return fn () => $this->filter(static fn ($value) => filled($value));
    }


    public function transform(): callable
    {
        return function ($value, bool $filter = true)
        {
            if ($value === null || $value === '' || $value === []) {
                return [];
            }

            if ($value instanceof \Closure) {
                $value = $value();
            }

            if (is_array($value)) {
            } elseif ($value instanceof Jsonable) {
                $value = json_decode($value->toJson(), true);
            } elseif ($value instanceof Arrayable) {
                $value = $value->toArray();
            } elseif (is_string($value)) {
                $array = null;

                try {
                    $array = json_decode($value, true);
                } catch (\Throwable) {
                }

                $value = is_array($array) ? $array : explode(',', $value);
            } else {
                $value = (array) $value;
            }

            return $filter ? array_filter($value, function ($v) {
                return $v !== '' && $v !== null;
            }) : $value;
        };
    }

    public function tree(): callable
    {
        return function(array $list, int $parentId = 0): array
        {
            $data = [];
            foreach ($list as $key => $item) {
                if ($item['parent_id'] == $parentId) {
                    $children = $this->tree($list, (int) $item['id']);
                    ! empty($children) && $item['children'] = $children;
                    $data[] = $item;
                    unset($list[$key]);
                }
            }

            return $data;
        };
    }

    public function reduceWithKeys(): callable
    {
        return function(array $array, callable $callback, $carry = null): mixed
        {
            foreach ($array as $key => $value) {
                $carry = $callback($carry, $value, $key);
            }

            return $carry;
        };
    }

    public function existEmpty(): callable
    {
        return function(array $array, $key): bool {
            return array_key_exists($key, $array) && empty($array[$key]);
        };
    }
}
