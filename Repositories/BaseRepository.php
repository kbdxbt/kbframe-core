<?php

declare(strict_types=1);

namespace Modules\Core\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
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

        if (!$model instanceof Model) {
            throw new \InvalidArgumentException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    public function query(): Builder
    {
        return $this->getModel()->query();
    }

    public function create($attributes): bool
    {
        return $this->getModel()->fill($attributes)->save();
    }

    public function update($attributes, $id): bool
    {
        $model = $this->query()->findOrFail($id);

        return $model->fill($attributes)->save();
    }

    public function updateOrInsert($attributes, $values = []): bool
    {
        if (! $id = $this->query()->where($attributes)->value('id')) {
            return $this->create(array_merge($attributes, $values));
        }

        if (empty($values)) {
            return true;
        }

        return $this->update($values, $id);
    }

    public function delete($keyValue, $isForce = false): void
    {
        $keyValues = Str::split($keyValue);

        if ($isForce) {
            $this->query()->whereIn($this->getModel()->getKeyName(), $keyValues)->forceDelete();
        } else {
            $this->query()->whereIn($this->getModel()->getKeyName(), $keyValues)->delete();
        }
    }

    public function recovery($ids): void
    {
        $ids = Str::split($ids);

        $this->getModel()::withTrashed()->whereIn($this->getModel()->getKeyName(), $ids)->restore();
    }

    public function batchUpdateByKeyName($keyValue, $attributes): bool
    {
        $keyValues = Str::split($keyValue);

        return $this->getModel()->update($attributes, [$this->getKeyName() => $keyValues]);
    }

    public function getId(): int
    {
        return $this->getModel()?->id;
    }

    public function getKeyName(): string
    {
        return $this->getModel()->getKeyName();
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
