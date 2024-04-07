<?php

namespace Modules\Core\Rules;

final class ChineseWordRule extends RegexRule
{
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/[\x{4e00}-\x{9fa5}]+/u';
    }
}
