<?php

namespace Modules\Core\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Jiannei\Response\Laravel\Providers\LaravelServiceProvider;
use Modules\Core\Providers\CoreServiceProvider;
use Modules\Core\Tests\Controllers\MemberController;
use Modules\Core\Tests\Seeder\MemberSeeder;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected $modulePath = __DIR__.'/Modules/';

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase();
        $this->withFactories(__DIR__.'/Factories/');
        $this->seed(MemberSeeder::class);
        $this->clearTestModulePath();
    }

    protected function tearDown(): void
    {
        $this->clearTestModulePath();
    }

    protected function setUpDatabase(): void
    {
        $this->app['db']
            ->connection()
            ->getSchemaBuilder()
            ->create('members', static function (Blueprint $blueprint): void {
                $blueprint->bigIncrements('id');
                $blueprint->string('name');
                $blueprint->string('email')->unique();
                $blueprint->timestamp('email_verified_at')->nullable();
                $blueprint->string('password');
                $blueprint->rememberToken();
                $blueprint->timestamps();
            });

    }

    protected function getPackageProviders($app): array
    {
        return [
            CoreServiceProvider::class,
            LaravelServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $this->registerTestModulePath($app);
    }

    protected function defineRoutes($router): void
    {
        Route::post('member_create', [MemberController::class, 'create']);
        Route::get('member_list', [MemberController::class, 'index']);
    }

    protected function registerTestModulePath($app): void
    {
        if (! is_dir($this->modulePath)) {
            File::makeDirectory(path: $this->modulePath);
        }
        if (! is_dir($this->modulePath.'kbframe-test')) {
            File::link(__DIR__.'/../', $this->modulePath.'kbframe-test');
        }

        $app['config']->set('modules.scan.enabled', true);
        $app['config']->set('modules.scan.paths', [__DIR__.'/../vendor/kbdxbt/*', __DIR__.'/../Tests/Modules/*']);
    }

    protected function clearTestModulePath(): void
    {
        if (is_dir($this->modulePath.'kbframe-test')) {
            @rmdir($this->modulePath.'kbframe-test');
        }
        if (is_dir($this->modulePath)) {
            File::deleteDirectory($this->modulePath);
        }
    }
}
