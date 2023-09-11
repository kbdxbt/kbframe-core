<?php

namespace Modules\Core\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Route;
use Jiannei\Response\Laravel\Providers\LaravelServiceProvider;
use Modules\Core\Providers\CoreServiceProvider;
use Modules\Core\Tests\Controllers\MemberController;
use Modules\Core\Tests\Seeder\MemberSeeder;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase();
        $this->withFactories(__DIR__.'/Factories/');
        $this->seed(MemberSeeder::class);
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

        config()->set('app.debug', true);
    }

    protected function defineRoutes($router): void
    {
        Route::post('member_create', [MemberController::class, 'create']);
        Route::get('member_list', [MemberController::class, 'index']);
    }
}
