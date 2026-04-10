<?php 

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserManagementService {

    private function ensureNotLastSuperAdmin(User $user): void
    {
        if ($user->hasRole('super-admin')) {
            $count = User::role('super-admin')->count();

            if ($count <= 1) {
                throw new Exception('Impossible de modifier le dernier super-admin.');
            }
        }
    }

    /* 
    Users
     */

    public function listUsers()
    {
        return User::with('roles')->latest()->get();
    }

    public function createUser(array $data): User {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'], 
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole($data['role']); 

        return $user->load('roles');
    }

    public function updateUser(User $user, array $data): User
    {
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        // Si le rôle change, on applique la règle
        if (isset($data['role'])) {
            return $this->updateUserRole($user, $data['role']);
        }

        return $user->load('roles');
    }


    public function deleteUser(User $user): void
    {
        if ($user->hasRole('super-admin') && User::role('super-admin')->count() <= 1) {
            throw new Exception('Impossible de supprimer le dernier super-admin.');
        }

        $user->delete();
    }


    /* 
    Roles
    */

    public function listRoles()
    {
        return Role::with('permissions')
            ->select('id','name')
            ->orderBy('name')
            ->get();
    }

    public function updateUserRole(User $user, string $role): User
    {
        $isCurrentlySuperAdmin = $user->hasRole('super-admin');
        $isBecomingSuperAdmin = $role === 'super-admin';

        if ($isCurrentlySuperAdmin && ! $isBecomingSuperAdmin) {
            // On retire le rôle super-admin
            $count = User::role('super-admin')->count();
            if ($count <= 1) {
                throw new Exception('Impossible de retirer le dernier super-admin.');
            }
        }

        $user->syncRoles([$role]);

        return $user->load('roles');
    }


    public function createRole(array $data): Role
    {
        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        $permissions = array_unique([
            'create tasks',
            ...($data['permissions'] ?? []),
        ]);

        $role->syncPermissions($permissions);

        return $role->load('permissions');
    }

    public function updateRolePermissions(Role $role, array $data): Role
    {
        $permissions = array_unique([
            'create tasks',
            ...$data['permissions'] ?? [],
        ]);

        $role->syncPermissions($permissions);

        return $role->load('permissions');
    }

    public function deleteRole(Role $role): void
    {
        if ($role->name === 'super-admin') {
            throw new Exception('Impossible de supprimer le rôle super-admin.');
        }

        $role->delete();
    }

    /* 
      Permissions
    */

    public function listPermissions()
    {
        return Permission::select('id', 'name')->get();
    }
}
