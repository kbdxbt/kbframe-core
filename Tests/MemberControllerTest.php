<?php

it('can member create', function () {
    $response = $this->postJson('member_create');
    $response->assertStatus(422);

    $response = $this->postJson('member_create', ['name' => 'test']);
    $response->assertStatus(200);
});

it('can member list', function () {
    $response = $this->getJson('member_list');
    $response->assertStatus(200);
});