<?php

namespace Modules\Core\Tests;

class MemberControllerTest extends TestCase
{
    public function testCreate(): void
    {
        $response = $this->postJson('member_create');
        $response->assertStatus(422);

        $response = $this->postJson('member_create', ['name' => 'test']);
        $response->assertStatus(200);
    }

    public function testIndex(): void
    {
        $response = $this->getJson('member_list');
        $response->assertStatus(200);
    }
}
