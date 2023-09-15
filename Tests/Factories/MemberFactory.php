<?php

declare(strict_types=1);

namespace Modules\Core\Tests\Factories;

use Illuminate\Support\Str;
use Modules\Core\Tests\Models\Member;

/** @var \Illuminate\Database\Eloquent\Factory $factory $factory */
$factory->define(Member::class, static fn ($faker): array => [
    'name' => $faker->name,
    'email' => $faker->unique()->safeEmail,
    'email_verified_at' => now(),
    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
    'remember_token' => Str::random(10),
]);
