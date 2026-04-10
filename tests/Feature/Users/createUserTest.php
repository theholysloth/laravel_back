<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('creates a new user', function () {
    $action = new CreateNewUser();

    $user = $action->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!'
    ]);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->email)->toBe('john@example.com');
    expect(Hash::check('Password123!', $user->password))->toBeTrue();
});
