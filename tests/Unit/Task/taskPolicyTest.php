<?php

use App\Policies\TaskPolicy;
use App\Models\User;
use App\Models\Task;

it('allows the owner to update the task', function () {
    $user = mock(User::class)->makePartial();
    $user->id = 1;

    $task = mock(Task::class)->makePartial();
    $task->user_id = 1;

    $policy = new TaskPolicy();

    expect($policy->update($user, $task))->toBeTrue();
});

it('denies update if user is not the owner', function () {
    $user = mock(User::class)->makePartial();
    $user->id = 1;

    $task = mock(Task::class)->makePartial();
    $task->user_id = 2;

    $policy = new TaskPolicy();

    expect($policy->update($user, $task))->toBeFalse();
});
