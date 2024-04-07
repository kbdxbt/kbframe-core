<?php

namespace Modules\Core\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

/**
 * @property string $name
 * @property string $description
 * @property string $type
 * @property array $keywords
 * @property string $homepage
 * @property string $license
 * @property array $authors
 * @property array $require
 * @property array $require_dev
 * @property array $suggest
 * @property array $autoload
 * @property array $autoload_dev
 * @property array $scripts
 * @property array $extra
 * @property string $version
 */
class ComposerProperty implements Arrayable
{
    protected array $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * @param  null  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * @return $this
     */
    public function set($key, $val)
    {
        $new = $this->attributes;

        Arr::set($new, $key, $val);

        return new static($new);
    }

    /**
     * @return $this
     */
    public function delete($key)
    {
        $new = $this->attributes;

        Arr::forget($new, $key);

        return new static($new);
    }

    /**
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get(str_replace('_', '-', $name));
    }

    public function toArray()
    {
        return $this->attributes;
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }
}
