<?php
use Spatie\Permission\Models\Role;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


test('super_admin',function(){
    Permission::create([
        'name' => 'view users',
        'guard_name' => 'web'
    ]);
    $role = Role::create(['name' => 'super-admin','guard_name' => 'web']); //role et user
    $user = User::factory()->create(); 
    $user->assignRole($role);

    Sanctum::actingAs($user); //pour l'authentification
    $response = $this->getJson('/api/users');

    $response->assertStatus(200);
    $response->assertJsonStructure(['users']);

    expect($user->can('view users'))->toBeTrue();
}); 

