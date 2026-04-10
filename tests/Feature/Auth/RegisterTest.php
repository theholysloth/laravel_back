<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

uses(RefreshDatabase::class);

it('registers a new user and returns token', function () {
    $payload = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ];

    $response = $this->postJson('/api/register', $payload);

    $response->assertStatus(201);//assertCreated()
    $response->assertJsonStructure([
        'message',
        'user' => ['id', 'name', 'email'],
        'token',
    ]);

    $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
});
