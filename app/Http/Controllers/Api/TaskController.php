<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private TaskService $service) {}

    public function index(Request $request)
    {
        $tasks = $this->service->listTasks($request->user());
        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $this->service->createTask($request->user(), $request->validated());
        return response()->json(new TaskResource($task), 201);
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $updated = $this->service->updateTask($task, $request->validated());

        return new TaskResource($updated);
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $this->service->deleteTask($task);

        return response()->noContent();
    }
}
