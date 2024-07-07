<?php

declare(strict_types=1);

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SebastianBergmann\Timer\ResourceUsageFormatter;
use SebastianBergmann\Timer\Timer;

if (! function_exists('environment')) {
    function environment(): string
    {
        if (defined('STDIN')) {
            return 'cli';
        }

        if ('cli' === PHP_SAPI) {
            return 'cli';
        }

        if (stripos(PHP_SAPI, 'cgi') !== false && getenv('TERM')) {
            return 'cli';
        }

        if (empty($_SERVER['REMOTE_ADDR']) && ! isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0) {
            return 'cli';
        }

        return 'web';
    }
}

if (! function_exists('format_bits')) {
    function format_bits(int $bits, $precision = 2, $suffix = true)
    {
        if ($bits > 0) {
            $i = floor(log($bits) / log(1000));

            if (! $suffix) {
                return round($bits / (1000 ** $i), $precision);
            }

            $sizes = ['B', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb'];

            return sprintf('%.02F', round($bits / (1000 ** $i), $precision)) * 1 .' '.@$sizes[$i];
        }

        return 0;
    }
}

if (! function_exists('format_bytes')) {
    function format_bytes(int $bytes, $precision = 2)
    {
        if ($bytes > 0) {
            $i = floor(log($bytes) / log(1024));

            $sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

            return sprintf('%.02F', round($bytes / (1024 ** $i), $precision)) * 1 .' '.@$sizes[$i];
        }

        return 0;
    }
}

if (! function_exists('bytes_to_bits')) {
    function bytes_to_bits(int $bytes)
    {
        if ($bytes > 0) {
            return $bytes * 8;
        }

        return 0;
    }
}

if (! function_exists('memoize')) {
    function memoize(callable $function): callable
    {
        return function () use ($function) {
            static $cache = [];

            $args = func_get_args();
            $key = serialize($args);
            $cached = true;

            if (! isset($cache[$key])) {
                $cache[$key] = $function(...$args);
                $cached = false;
            }

            return ['result' => $cache[$key], 'cached' => $cached];
        };
    }
}

if (! function_exists('once')) {
    function once(callable $function): callable
    {
        return function (...$args) use ($function) {
            static $called = false;
            if ($called) {
                return;
            }
            $called = true;

            return $function(...$args);
        };
    }
}

if (! function_exists('is_json')) {
    /**
     * If the string is valid JSON, return true, otherwise return false
     *
     * @param  string  $str  the string to check
     * @return bool the function is_json() is returning a boolean value
     */
    function is_json(string $str): bool
    {
        json_decode($str);

        return json_last_error() === JSON_ERROR_NONE;
    }
}

if (! function_exists('validate')) {
    function validate(array $data = [], array $rules = [], array $messages = [], array $customAttributes = []): array
    {
        return validator($data, $rules, $messages, $customAttributes)->validate();
    }
}

if (! function_exists('call')) {
    function call($callback, array $parameters = [], ?string $defaultMethod = null): void
    {
        app()->call($callback, $parameters, $defaultMethod);
    }
}

if (! function_exists('catch_resource_usage')) {
    function catch_resource_usage($callback, ...$parameter): string
    {
        $timer = new Timer();
        $timer->start();

        app()->call($callback, $parameter);

        return (new ResourceUsageFormatter())->resourceUsage($timer->stop());
    }
}

if (! function_exists('resolve_class_from')) {
    function resolve_class_from(string $path, ?string $vendorPath = null, ?string $vendorNamespace = null): string
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $vendorPath = $vendorPath ? realpath($vendorPath) : app_path();
        $vendorNamespace ??= config('modules.namespace'); // App\

        return str(realpath($path))
            ->replaceFirst($vendorPath, $vendorNamespace)
            ->replaceLast('.php', '')
            ->replace(DIRECTORY_SEPARATOR, '\\')
            ->replace('\\\\', '\\')
            ->start('\\')
            ->toString();
    }
}

/*
 * 写入日志
 *
 * @note single 创建单个日志文件 daily 定期清理日志文件
 */
if (! function_exists('write_log')) {
    function write_log($path, $data, $context = [], $config = []): void
    {
        if (empty($config['log_file'])) {
            $log_file = Carbon\Carbon::now()->format($config['format'] ?? 'Ymd').'.log';
        } else {
            $log_file = $config['log_file'];
        }
        $path = storage_path('logs'.DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$log_file);

        if (! is_string($data)) {
            $data = (string) json_encode(\Illuminate\Support\Arr::transform($data), JSON_UNESCAPED_UNICODE);
        }

        Log::build(array_merge(['driver' => 'single', 'path' => $path], $config))->info($data, $context);
    }
}

if (! function_exists('catch_query_log')) {
    function catch_query_log($callback, ...$parameter): array
    {
        return (new Pipeline(app()))
            ->send($callback)
            ->through(function ($callback, Closure $next) {
                DB::enableQueryLog();
                DB::flushQueryLog();

                $queryLog = $next($callback);

                DB::disableQueryLog();

                return $queryLog;
            })
            ->then(function ($callback) use ($parameter) {
                app()->call($callback, $parameter);

                return DB::getQueryLog();
            });
    }
}

if (! function_exists('dump_to_array')) {
    function dump_to_array(...$vars): void
    {
        foreach ($vars as $var) {
            ($var instanceof Arrayable or method_exists($var, 'toArray')) ? dump($var->toArray()) : dump($var);
        }
    }
}

if (! function_exists('generate_random')) {
    function generate_random($type = 'alnum', $len = 8)
    {
        switch ($type) {
            case 'alpha':
            case 'alnum':
            case 'numeric':
            case 'nozero':
                $pool = '';

                switch ($type) {
                    case 'alpha':
                        $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

                        break;

                    case 'alnum':
                        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

                        break;

                    case 'numeric':
                        $pool = '0123456789';

                        break;

                    case 'nozero':
                        $pool = '123456789';

                        break;
                }

                return substr(str_shuffle(str_repeat($pool, (int) ceil($len / strlen($pool)))), 0, $len);

            case 'unique':
            case 'md5':
                return md5(uniqid(mt_rand(), true));

            case 'encrypt':
            case 'sha1':
                return sha1(uniqid(mt_rand(), true));
        }

        return '';
    }
}

if (! function_exists('dd_to_array')) {
    function dd_to_array(...$vars): void
    {
        dump_to_array(...$vars);

        exit(1);
    }
}

if (! function_exists('amis')) {
    function amis($type = null)
    {
        if (filled($type)) {
            return \Modules\Core\Renderers\Component::make()->setType($type);
        }

        return \Modules\Core\Renderers\Amis::make();
    }
}

if (! function_exists('amisMake')) {
    function amisMake()
    {
        return \Modules\Core\Renderers\Amis::make();
    }
}

if (! function_exists('pd')) {
    function pd(...$vars): void
    {
        pp(...$vars);

        exit(1);
    }
}

if (! function_exists('pp')) {
    function pp(...$vars): void
    {
        foreach ($vars as $var) {
            /** @noinspection DebugFunctionUsageInspection */
            highlight_string(sprintf("\n<?php\n\$var = %s;\n?>\n", var_export($var, true)));
        }
    }
}

if (! function_exists('export_csv')) {
    function export_csv($list, $filename): bool
    {
        if (!is_array($list)) {
            return false;
        }

        ob_start();

        $fp = fopen($filename, 'a');

        if (!empty($list)) {
            $size = ceil(count($list) / 500);

            for ($i = 0; $i < $size; $i++) {
                $buffer = array_slice($list, $i * 500, 500);

                foreach ($buffer as $k => $row) {
                    fputcsv($fp, array_values(\Illuminate\Support\Arr::transform($row)));
                    unset($data, $buffer[$k]);
                }
            }
        }

        fclose($fp);
        ob_end_clean();

        return true;
    }
}
