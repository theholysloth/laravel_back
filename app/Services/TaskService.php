<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskService
{

    public function listTasks(User $user): LengthAwarePaginator
    {
        if ($user->can('view all tasks')) {
            return Task::latest()->paginate();
        }

        return $user->tasks()->latest()->paginate();
    }

    public function createTask(User $user, array $data): Task
    {
        return $user->tasks()->create($data);
    }


    public function updateTask(Task $task, array $data): Task
    {
        $task->update($data);
        return $task;
    }

    public function deleteTask(Task $task): void
    {
        $task->delete();
        
    }
}
