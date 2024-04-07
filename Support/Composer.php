<?php

namespace Modules\Core\Support;

use Composer\Autoload\ClassLoader;

class Composer
{
    protected static array $files = [];

    /**
     * @var ClassLoader
     */
    protected static $loader;

    /**
     * 获取 composer 类加载器.
     *
     * @return ClassLoader
     */
    public static function loader()
    {
        if (! static::$loader) {
            static::$loader = include base_path().'/vendor/autoload.php';
        }

        return static::$loader;
    }

    /**
     * @return ComposerProperty
     */
    public static function parse(?string $path)
    {
        return new ComposerProperty(static::fromJson($path));
    }

    /**
     * @return null
     */
    public static function getVersion(?string $packageName, ?string $lockFile = null)
    {
        if (! $packageName) {
            return null;
        }

        $lockFile = $lockFile ?: base_path('composer.lock');

        $content = collect(static::fromJson($lockFile)['packages'] ?? [])
            ->filter(function ($value) use ($packageName) {
                return $value['name'] == $packageName;
            })->first();

        return $content['version'] ?? null;
    }

    /**
     * @return array
     */
    public static function fromJson(?string $path)
    {
        if (isset(static::$files[$path])) {
            return static::$files[$path];
        }

        if (! $path || ! is_file($path)) {
            return static::$files[$path] = [];
        }

        try {
            return static::$files[$path] = (array) json_decode(app('files')->get($path), true);
        } catch (\Throwable $e) {
        }

        return static::$files[$path] = [];
    }
}
