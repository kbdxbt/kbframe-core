<?php

namespace Modules\Core\Rules;

final class ExplodeRule extends Rule
{
    protected $type;

    protected $separator;

    public function __construct($type = 'number', $separator = ',')
    {
        if (! in_array($type, ['number', 'string'])) {
            throw new \InvalidArgumentException('The explode rule type must be a number or string.');
        }

        $this->type = $type;
        $this->separator = $separator;
    }

    public function passes($attribute, $value)
    {
        $values = explode(',', $value);

        foreach ($values as $val) {
            if ($this->type == 'number' && !is_numeric($val)) {
                return false;
            }
            if ($this->type == 'string' && !is_string($val)) {
                return false;
            }
        }

        return true;
   }
}
