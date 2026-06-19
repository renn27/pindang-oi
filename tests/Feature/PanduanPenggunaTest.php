<?php

use App\Models\User;

test('panduan pengguna screen is protected from guests', function () {
    $response = $this->get('/panduan-pengguna');

    $response->assertRedirect('/login');
});

test('panduan pengguna screen can be rendered for authenticated user', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/panduan-pengguna');

    $response->assertOk();
});

test('panduan developer screen is protected from guests', function () {
    $response = $this->get('/panduan-developer');

    $response->assertRedirect('/login');
});

test('panduan developer screen can be rendered for authenticated user', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/panduan-developer');

    $response->assertOk();
});
