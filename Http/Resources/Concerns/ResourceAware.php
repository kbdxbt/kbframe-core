<?php

declare(strict_types=1);

namespace Modules\Core\Http\Resources\Concerns;

use Modules\Core\Support\Traits\Castable;

trait ResourceAware
{
    use Castable;

    protected array $withoutFields = [];

    protected string $currentScene = '';

    protected array $scene = [];

    private bool $hide = true;

    /**
     * 设置场景
     *
     * @return $this
     */
    public function scene(string $scene): static
    {
        $this->currentScene = $scene;

        return $this;
    }

    /**
     * @return $this
     */
    public function hide(array $fields): static
    {
        $this->withoutFields = $fields;

        return $this;
    }

    /**
     * @return $this
     */
    public function show(array $fields): static
    {
        $this->withoutFields = $fields;
        $this->hide = false;

        return $this;
    }

    public function wrapCollect(array $value, ?string $method = 'toArray'): mixed
    {
        $this->cast($value);
        if ($this->currentScene && ! empty($sceneFields = $this->scene[$this->currentScene])) {
            $collect = collect($value)->only($sceneFields);
        } elseif (! $this->hide) {
            $collect = collect($value)->only($this->withoutFields);
        } else {
            $collect = collect($value)->except($this->withoutFields);
        }

        return $collect->{$method}();
    }
}
