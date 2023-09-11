<?php

declare(strict_types=1);

namespace Modules\Core\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class CoreServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Core';

    protected string $moduleNameLower = 'core';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerRequestId();

        // 低版本 MySQL(< 5.7.7) 或 MariaDB(< 10.2.2)，则可能需要手动配置迁移生成的默认字符串长度，以便按顺序为它们创建索引。
        Schema::defaultStringLength(191);

        Log::shareContext(['request_id' => app('request_id')]);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * Register request id.
     */
    protected function registerRequestId(): void
    {
        $this->app->singleton('request_id', fn () => (string) Str::uuid());
    }
}
