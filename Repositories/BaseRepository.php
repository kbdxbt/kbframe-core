<?php

declare(strict_types=1);

namespace Modules\Core\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Support\Traits\CreateStaticable;

abstract class BaseRepository
{
    use CreateStaticable;

    /**
     * @var Model
     */
    protected $model;

    public function __construct()
    {
        $this->makeModel();
        $this->boot();
    }

    public function boot(): void
    {
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function resetModel(): void
    {
        $this->makeModel();
    }

    abstract public function model(): string;

    public function makeModel(): Model
    {
        $model = app()->make($this->model());

        if (! $model instanceof Model) {
            throw new \InvalidArgumentException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    public function query(): Builder
    {
        return $this->getModel()->query();
    }

    public static function __callStatic($method, $arguments)
    {
        return call_user_func_array([self::make(), $method], $arguments);
    }

    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->model, $method], $arguments);
    }
}
