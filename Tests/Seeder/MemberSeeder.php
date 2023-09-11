<?php

namespace Modules\Core\Tests\Seeder;

use Illuminate\Database\Seeder;
use Modules\Core\Tests\Models\Member;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        factory(Member::class, 5)->create();
    }
}