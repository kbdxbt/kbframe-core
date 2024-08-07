<?php

declare(strict_types=1);

namespace Modules\Core\Support\Macros;

use Illuminate\Support\Str;

/**
 * @mixin \Illuminate\Support\Str
 */
class StrMacro
{
    public static function appendIf(): callable
    {
        return static fn ($value, $suffix) => Str::endsWith($value, $suffix) ? $value : $value.$suffix;
    }

    public static function prependIf(): callable
    {
        return static fn ($value, $prefix) => Str::startsWith($value, $prefix) ? $value : $prefix.$value;
    }

    public static function mbSubstrCount(): callable
    {
        // return fn($haystack, $needle, $encoding = null) => mb_substr_count($haystack, $needle, $encoding);

        return static fn ($haystack, $needle, $encoding = null) => mb_substr_count($haystack, $needle, $encoding);
    }

    public static function pipe(): callable
    {
        return static fn ($value, callable $callback) => $callback($value);
    }

    /**
     * @see https://github.com/koenhendriks/laravel-str-acronym
     */
    public static function acronym(): callable
    {
        return function ($string, $delimiter = '') {
            if (empty($string)) {
                return '';
            }

            $acronym = '';
            foreach (preg_split('/[^\p{L}]+/u', $string) as $word) {
                if (! empty($word)) {
                    $first_letter = mb_substr($word, 0, 1);
                    $acronym .= $first_letter.$delimiter;
                }
            }

            return $acronym;
        };
    }

    public static function split(): callable
    {
        return function($value, $replaceArr = [" ", "\r\n", "\r", "\n", PHP_EOL, "，", "/", ";", "。", "；", ","]): array
        {
            if (empty($value)) return [];
            if (is_array($value)) return $value;

            $delimiter = ',';
            return str($value)
                ->replace($replaceArr, $delimiter)
                ->explode($delimiter)
                ->filter()->unique()->values()->toArray();
        };
    }
}
