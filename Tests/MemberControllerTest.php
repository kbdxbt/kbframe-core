<?php

it('can member create', function () {
    $response = \Pest\Laravel\postJson('member_create');
    $response->assertStatus(422);

    $response = \Pest\Laravel\postJson('member_create', ['name' => 'test']);
    $response->assertStatus(200);
});

it('can member list', function () {
    $response = \Pest\Laravel\getJson('member_list');
    $response->assertStatus(200);
});
