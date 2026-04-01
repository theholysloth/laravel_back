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

    public function updateRole(UpdateUserRoleRequest $request, User $user)
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
        $roles = Role::query()
                ->select('id','name')
                ->orderBy('name')
                ->get();
        return response()->json([
            'roles' => $roles
        ]);
    }

    public function store(CreateUserByAdminRequest $request){
        $this->authorize('assignRole', User::class);

        $validated = $request->validated();
        $user = User::create([
            'nom' =>$validated['nom'], 
            'email' =>$validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);


        return response()->json([
            'message' => 'Utilisateur créé!',
            'user' => $user, 
        ],201);
    }
}
