<?php

namespace Modules\Core\Tests;

it('can member create', function () {
    $this->postJson('member_create')->assertStatus(422);
    $this->postJson('member_create', ['name' => 'test'])->assertStatus(200);
});

it('can member list', function () {
    $this->getJson('member_list')->assertStatus(200);
});
