<?php

namespace Modules\Core\Support\Traits;

use Illuminate\Cache\CacheManager;

trait Cacheable
{
    /** @var mixed */
    protected $key;

    protected string $cachePrefix;

    protected static CacheManager|null $cache = null;

    public static function setCacheInstance(CacheManager $cache): void
    {
        self::$cache = $cache;
    }

    public static function getCacheInstance(): CacheManager
    {
        if (self::$cache === null) {
            self::$cache = app('cache');
        }

        return self::$cache;
    }

    protected function getCacheKey(): string
    {
        return $this->cachePrefix.$this->key;
    }
}
