<?php
/*///////////////////////////////////////////////////////////////////////////////////////////////////////

N'EST PAS BRANCHE AUX ROUTES , PERMET DE COMPRENDRE LE MECHANISME DE FORTIFY

///////////////////////////////////////////////////////////////////////////////////////////////*/
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\LoginUserRequest; 
use App\Http\Requests\RegisterUserRequest; 
use Illuminate\Support\Facades\Hash;



class AuthController extends Controller
{
    /*
    Fortify installe automatiquement
        -  Les routes /login, /register, /logout grace à php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"
        - les actions d'authentification dans le dossier Actions
        
        
        
        
        */



    public function login(LoginUserRequest $request){
        $validated = $request->validated(); //on recupere les données validées par le form request  Fortify::authenticateUsing(...)

        $user = User::where('email', $validated['email'])->first();
        if (!$user || !Hash::check($validated['password'], $user->password)){
            return response()->json(['message' => ' informations erronées'], 401); 
        }

        $token = $user->createToken('api-token')->plainTextToken; //generation d'un Api token avec sanctum

        return response()->json([
            'message' => 'Connexion reussie ',
            'user' => $user->load('roles'), 
            'token' => $token
        ]);
    }

    public function register(RegisterUserRequest $request){
        $validated = $request->validated();
        $user = User::create([
            'name' =>$validated['name'], 
            'email' =>$validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('api-token')->plainTextToken; 

        return response()->json([
            'message' => 'Utilisateur enregistré!',
            'user' => $user, 
            'token' => $token
        ],201);
    }

    public function logout( Request $request){
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'deconnexion reussie!',
        ]);
    }

    /*public function me(Request $request){
        return response()->json([
            'user' => $request->user(),
        ]);
    }*/
    
}
