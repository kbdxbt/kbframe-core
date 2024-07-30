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

    protected function boot(): void
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

    public function insertById($attributes): int
    {
        $this->getModel()->fill($attributes)->save();

        return $this->getId();
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

    public function delete($keyValue, $isForce = false, $keyName = ''): void
    {
        $keyValues = Str::split($keyValue);
        $keyName = $keyName ? : $this->getModel()->getKeyName();

        if ($isForce) {
            $this->query()->whereIn($keyName, $keyValues)->forceDelete();
        } else {
            $this->query()->whereIn($keyName, $keyValues)->delete();
        }
    }

    public function recovery($ids, $keyName = ''): void
    {
        $ids = Str::split($ids);

        $keyName = $keyName ? : $this->getModel()->getKeyName();

        $this->getModel()::withTrashed()->whereIn($keyName, $ids)->restore();
    }

    public function batchUpdateByKeyName($keyValue, $attributes, $keyName = ''): int
    {
        $keyValues = Str::split($keyValue);

        $keyName = $keyName ? : $this->getModel()->getKeyName();

        return $this->query()->whereIn($keyName, $keyValues)->update($attributes);
    }

    public function getId(): int
    {
        return $this->getModel()?->id;
    }

    public function getKeyName(): string
    {
        return $this->getModel()->getKeyName();
    }

    public function searchFields(): array
    {
        return [];
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
