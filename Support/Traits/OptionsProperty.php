<?php

namespace Modules\Core\Support\Traits;

trait OptionsProperty
{
    protected array $options = [];

    public function __call($method, $parameters)
    {
        $this->options[$method] = \count($parameters) > 0 ? $parameters[0] : true;

        return $this;
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value): void
    {
        $this->offsetSet($key, $value);
    }

    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    public function __unset($key): void
    {
        $this->offsetUnset($key);
    }

    public function set($name, $value = null): void
    {
        $options = \is_array($name) ? $name : [$name => $value];

        $this->options = array_merge($this->options, $options);
    }

    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return $this->options;
        }

        if (\array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }

        return value($default);
    }

    public function offsetExists($offset)
    {
        return isset($this->options[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        unset($this->options[$offset]);
    }
}
