<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Http\Request;
use App\Http\Requests\CreateUserByAdminRequest;
use App\Http\Requests\UpdateUserByAdminRequest;
use App\Http\Requests\UpdateUserRoleRequest;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class UserManagementController extends Controller
{

    use AuthorizesRequests;
    
    public function __construct(private UserManagementService $service) {}

    /*Users*/

    public function index()
    {
        $this->authorize('viewAny', User::class);

        return response()->json([
            'users' => $this->service->listUsers()
        ]);
    }

    public function store(CreateUserByAdminRequest $request)
    {
        $this->authorize('create', User::class);

        $user = $this->service->createUser($request->validated());

        return response()->json([
            'message' => 'Utilisateur créé!',
            'user' => $user,
        ], 201);
    }

    public function update(UpdateUserByAdminRequest $request, User $user)
    {
        $this->authorize('assignRole', $user);

        $updated = $this->service->updateUser($user, $request->validated());

        return response()->json([
            'message' => 'Utilisateur modifié!',
            'user' => $updated,
        ]);
    }

    public function destroy(User $user)
    {
        $this->authorize('assignRole', $user);

        $this->service->deleteUser($user);

        return response()->json([
            'message' => 'Utilisateur supprimé!',
        ]);
    }

    /*Roles*/

    public function roles()
    {
        $this->authorize('viewAny', User::class);

        return response()->json([
            'roles' => $this->service->listRoles()
        ]);
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user)
    {
        $this->authorize('assignRole', $user);

        $updated = $this->service->updateUserRole($user, $request->validated()['role']);

        return response()->json([
            'message' => 'Rôle mis à jour avec succès',
            'user' => $updated,
        ]);
    }

    public function storeRole(Request $request)
    {
        $this->authorize('assignRole', User::class);

        $role = $this->service->createRole($request->validate([
            'name' => ['required', 'string', 'unique:roles,name'],
            'permissions' => ['required', 'array', 'min:1'],
        ]));

        return response()->json([
            'message' => 'Role créé!',
            'role' => $role,
        ], 201);
    }

    public function updateRolePermissions(Request $request, Role $role)
    {
        $this->authorize('assignRole', User::class);

        $updated = $this->service->updateRolePermissions($role, $request->validate([
            'name' => ['required', 'string'],
            'permissions' => ['required', 'array', 'min:1'],
        ]));

        return response()->json([
            'message' => 'Rôle mis à jour avec succès',
            'role' => $updated,
        ]);
    }

    public function deleteRole(Role $role)
    {
        $this->authorize('assignRole', User::class);

        $this->service->deleteRole($role);

        return response()->json([
            'message' => 'Role supprimé!',
        ]);
    }

    /*Permissions*/

    public function permissions()
    {
        $this->authorize('assignRole', User::class);

        return response()->json([
            'permissions' => $this->service->listPermissions()
        ]);
    }
}
