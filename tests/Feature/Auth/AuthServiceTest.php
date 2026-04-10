<?php

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(AuthService::class);
});


it('logs in a user with correct credentials', function () {
    $user = User::factory()->create([
        'password' => Hash::make('secret123'),
    ]);

    $result = $this->service->login([
        'email' => $user->email,
        'password' => 'secret123',
    ]);

    expect($result['user']->id)->toBe($user->id);
    expect($result['token'])->not->toBeEmpty();
});


it('fails login with wrong password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('secret123'),
    ]);

    $this->service->login([
        'email' => $user->email,
        'password' => 'wrong',
    ]);
})->throws(Exception::class);


it('registers a new user', function () {
    $result = $this->service->register([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'secret123',
    ]);

    expect($result['user']->email)->toBe('john@example.com');
    expect($result['token'])->not->toBeEmpty();
});



it('logs out the user and deletes token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api-token')->plainTextToken;

    $response = $this->withToken($token)->postJson('/api/logout');

    $response->assertStatus(200);
    $response->assertJson(['message' => 'Déconnexion réussie!']);
    
    $this->assertDatabaseMissing('personal_access_tokens', [
        'tokenable_id' => $user->id,
    ]);
});


