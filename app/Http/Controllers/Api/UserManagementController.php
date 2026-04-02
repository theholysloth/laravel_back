<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\UpdateUserRoleRequest;
use Spatie\Permission\Models\Role;
use App\Http\Requests\CreateUserByAdminRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateUserByAdminRequest;
use Spatie\Permission\Models\Permission;



class UserManagementController extends Controller
{
    //
    use AuthorizesRequests; 
    public function index(){
        $this->authorize('viewAny',User::class);//on doit definir viewAny qui sera une policy qui verifie la permission 'view users'
        $users = User::with('roles')->latest()->get();//recuperer Users+roles

        return response()->json([
            'users' => $users
        ]);
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user)//role d'un user
    {
        $this->authorize('assignRole', $user);

        $validated = $request->validated();
        $newRole = $validated['role'];

        $isCurrentlySuperAdmin = $user->hasRole('super-admin');
        $isBecomingSuperAdmin = $newRole === 'super-admin';

        // Cas : on retire le rôle super-admin à un utilisateur
        if ($isCurrentlySuperAdmin && ! $isBecomingSuperAdmin) {
            $superAdminCount = User::role('super-admin')->count();

            if ($superAdminCount <= 1) {
                return response()->json([
                    'message' => 'Impossible de retirer le dernier super-admin.'
                ], 422);
            }
        }

        $user->syncRoles([$newRole]);

        return response()->json([
            'message' => 'Rôle mis à jour avec succès',
            'user' => $user->load('roles'),
        ]);
    }

    public function roles(){
        $this->authorize('viewAny',User::class);
        $roles = Role::with('permissions')   // ← AJOUT ICI
                ->select('id','name')
                ->orderBy('name')
                ->get();
        return response()->json([
            'roles' => $roles
        ]);
    }

    public function store(CreateUserByAdminRequest $request){
        $this->authorize('create', User::class);

        $validated = $request->validated();
        $user = User::create([
            'name' =>$validated['name'], 
            'email' =>$validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);


        return response()->json([
            'message' => 'Utilisateur créé!',
            'user' => $user->load('roles'), 
        ],201);
    }

    public function update(UpdateUserByAdminRequest $request, User $user){
        $this->authorize('assignRole', $user);

        $validated = $request->validated();
        $user->update([
            'name' =>$validated['name'], 
            'email' =>$validated['email'],
        ]);

        $user->syncRoles([$validated['role']]);


        return response()->json([
            'message' => 'Utilisateur modifié!',
            'user' => $user->load('roles'), 
        ],200);
    }

    public function destroy(User $user){
        $this->authorize('assignRole', $user);

        if($user->hasRole('super-admin')){
            $count = User::role('super-admin')->count();

            if($count <= 1 ){
                return response()->json([
                    'message' => 'Impossible de supprimer le dernier super-admin'
                ],422);
            }
        }
        $user->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé!',
        ]);
    }

    public function permissions(){
        $this->authorize('assignRole', User::class);
        return response()->json([
            'permissions' => Permission::select('id', 'name')->get()
        ]);
    }


    public function storeRole(Request $request){
        $this->authorize('assignRole', User::class);

        $validated = $request->validate([
            'name'=>['required', 'string', 'unique:roles,name'], 
            'permissions' => ['array']
        ]);
        $role = Role::create([
            'name' =>$validated['name'], 
        ]);

        if(!empty($validated['permissions'])){
            $role->syncPermissions($validated['permissions']?? []);
        }


        return response()->json([
            'message' => 'Role créé!',
            'role' => $role->load('permissions'), 
        ],201);
    }

    public function updateRolePermissions(Request $request, Role $role){
        $this->authorize('assignRole', User::class);

        $validated = $request->validate([
            'name'=> ['required', 'string'], 
            'permissions' => ['array']
        ]);

        $role->update([
            'name' =>$validated['name'], 
        ]);
        $role->syncPermissions($validated['permissions']??[]);

        return response()->json([
            'message' => 'Rôle mis à jour avec succès',
            'role' => $role->load('permissions'),
        ]);
    }

    public function deleteRole(Role $role){
        $this->authorize('assignRole', User::class);

        if($role->name === 'super-admin'){
            return response()->json([
                    'message' => 'Impossible de supprimer le role de super-admin'
                ],422);
        }
        $role->delete();

        return response()->json([
            'message' => 'Role supprimé!',
        ]);
    }


}
