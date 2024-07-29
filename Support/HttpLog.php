<?php

namespace Modules\Core\Support;

use Illuminate\Support\Arr;

abstract class HttpLog
{
    protected $driver;

    protected $exceptMethods = [];

    protected $exceptPaths = [];

    protected $removedHeaders = [];

    protected $removedInputs = [];

    protected static $skipCallbacks = [];

    protected $maxLengthOfMediumtext = 15 * 1024 * 1024;

    public function getDriver()
    {
        return $this->driver;
    }

    public function setDriver($driver): void
    {
        $this->driver = $driver;
    }

    protected function createHttpLog($request, $response): void
    {
        $collectData = $this->collectData($request, $response);

        if ($this->shouldLogHttp($request)) {
            if ($this->driver === 'mysql') {
                \Modules\System\Repositories\HttpLogRepository::make()->create($collectData);
            } else {
                write_log('http_log', $collectData);
            }
        }
    }

    protected function shouldLogHttp($request): bool
    {
        return ! $this->shouldntLogHttp($request);
    }

    protected function shouldntLogHttp($request): bool
    {
        if (\in_array($request->method(), array_map('strtoupper', $this->exceptMethods), true)) {
            return true;
        }

        foreach ($this->exceptPaths as $exceptPath) {
            $exceptPath === '/' or $exceptPath = trim($exceptPath, '/');
            if ($request->fullUrlIs($exceptPath) || $request->is($exceptPath)) {
                return true;
            }
        }

        return $this->shouldSkip($request);
    }

    protected function shouldSkip($request): bool
    {
        foreach (static::$skipCallbacks as $callback) {
            if ($callback($request)) {
                return true;
            }
        }

        return false;
    }

    protected function shouldntSkip($request): bool
    {
        return ! $this->shouldSkip($request);
    }

    protected function extractHeader($headers): string
    {
        $header = Arr::except(
            $headers,
            array_map('strtolower', $this->removedHeaders)
        );

        return (string) json_encode($header);
    }

    protected function extractInput($inputs): string
    {
        $data = Arr::except($inputs, $this->removedInputs);

        return (string) json_encode($data);
    }

    protected function calculateDuration(): string
    {
        return number_format(microtime(true) - (constant('LARAVEL_START') ?: microtime(true)), 3);
    }

    abstract protected function collectData($request, $response);
}
