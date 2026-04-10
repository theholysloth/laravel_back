<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

uses(RefreshDatabase::class);

it('logs in a user and returns token', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => bcrypt('Password123!'),
    ]);

    $payload = [
        'email' => 'john@example.com',
        'password' => 'Password123!',
    ];

    $response = $this->postJson('/api/login', $payload);

    $response->assertOk();
    $response->assertJsonStructure([
        'message',
        'user' => ['id', 'name', 'email'],
        'token',
    ]);
});
