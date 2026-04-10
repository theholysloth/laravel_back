<?php
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Task;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;




uses(RefreshDatabase::class);

it('allows admin to view all tasks', function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'view all tasks', 'guard_name' => 'web']);

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $admin->givePermissionTo('view all tasks'); // a la permission: view all tasks

    Task::factory()->count(3)->create(); // tasks de n'importe qui

    $response = $this->actingAs($admin)->getJson('/api/tasks');

    $response->assertStatus(200);
    $response->assertJsonCount(3, 'data');
});

it('shows only user tasks for normal users', function () {
    $user = User::factory()->create();

    Task::factory()->count(2)->create(['user_id' => $user->id]);
    Task::factory()->count(3)->create(); // autres users

    $response = $this->actingAs($user)->getJson('/api/tasks');

    $response->assertStatus(200);
    $response->assertJsonCount(2, 'data');
});



it('creates a task for the authenticated user', function () {
    $user = User::factory()->create();

    $payload = [
        'nom' => 'Ma tâche',
        'task' => 'Faire le test',
        'date' => '2026-04-07',
        'done' => false,
    ];


    $response = $this->actingAs($user)->postJson('/api/tasks', $payload);

    $response->assertCreated();
    $this->assertDatabaseHas('tasks', [
        'task' => 'Faire le test',
        'user_id' => $user->id,
    ]);
});


it('allows user to view their own task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(200);
});
it('forbids viewing a task that does not belong to the user', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(); // autre user

    $response = $this->actingAs($user)->getJson("/api/tasks/{$task->id}");

    $response->assertForbidden();
});

it('updates a task when authorized', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $payload = [
        'nom' => 'Ma tâche',
        'task' => 'Faire le test',
        'date' => '2026-04-07',
        'done' => false,
    ];


    $response = $this->actingAs($user)->putJson("/api/tasks/{$task->id}", $payload);

    $response->assertOk();
    $this->assertDatabaseHas('tasks', [
        'task' => 'Faire le test',
        'user_id' => $user->id,
    ]);
});

it('deletes a task when authorized', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->deleteJson("/api/tasks/{$task->id}");

    $response->assertNoContent();
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});
