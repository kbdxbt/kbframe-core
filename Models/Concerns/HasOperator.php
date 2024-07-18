<?php

namespace Modules\Core\Models\Concerns;

trait HasOperator
{
    const CREATED_BY = 'created_by';

    const UPDATED_BY = 'updated_by';

    public $operators = false;

    protected static $ignoreOperatorsOn = [];

    protected $guard = null;

    public function touchOperators($attribute = null)
    {
        if ($attribute) {
            $this->$attribute = $this->freshOperator();

            return $this->save();
        }

        if (! $this->usesOperators()) {
            return false;
        }

        $this->updateOperators();

        return $this->save();
    }

    public function touchQuietlyOperators($attribute = null)
    {
        return static::withoutEvents(fn () => $this->touchOperators($attribute));
    }

    public function updateOperators()
    {
        $operator = $this->freshOperator();

        $this->setUpdatedBy($operator);

        $createdByColumn = $this->getCreatedByColumn();

        if (! $this->exists && ! is_null($createdByColumn) && ! $this->isDirty($createdByColumn)) {
            $this->setCreatedBy($operator);
        }

        return $this;
    }

    public function setCreatedBy($value)
    {
        $this->{$this->getCreatedByColumn()} = $value;

        return $this;
    }

    public function setUpdatedBy($value)
    {
        $this->{$this->getUpdatedByColumn()} = $value;

        return $this;
    }

    public function freshOperator()
    {
        return Auth($this->guard)->id();
    }

    public function usesOperators()
    {
        return $this->operators && ! static::isIgnoringOperators($this::class);
    }

    public function getCreatedByColumn()
    {
        return static::CREATED_BY;
    }

    public function getUpdatedByColumn()
    {
        return static::UPDATED_BY;
    }

    public function getQualifiedCreatedByColumn()
    {
        return $this->qualifyColumn($this->getCreatedByColumn());
    }

    public function getQualifiedUpdatedByColumn()
    {
        return $this->qualifyColumn($this->getUpdatedByColumn());
    }

    public function setGuard($value)
    {
        $this->guard = $value;

        return $this;
    }

    public function getGuard()
    {
        return $this->guard;
    }


    /**
     * Disable timestamps for the current class during the given callback scope.
     *
     * @param  callable  $callback
     * @return mixed
     */
    public static function withoutOperators(callable $callback)
    {
        return static::withoutOperatorsOn([static::class], $callback);
    }

    public static function withoutOperatorsOn($models, $callback)
    {
        static::$ignoreOperatorsOn = array_values(array_merge(static::$ignoreOperatorsOn, $models));

        try {
            return $callback();
        } finally {
            static::$ignoreOperatorsOn = array_values(array_diff(static::$ignoreOperatorsOn, $models));
        }
    }

    public static function isIgnoringOperators($class = null)
    {
        $class ??= static::class;

        foreach (static::$ignoreOperatorsOn as $ignoredClass) {
            if ($class === $ignoredClass || is_subclass_of($class, $ignoredClass)) {
                return true;
            }
        }

        return false;
    }
}
