<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $service) {}

    public function login(LoginUserRequest $request)
    {
        try {
            $result = $this->service->login($request->validated());

            return response()->json([
                'message' => 'Connexion réussie',
                'user' => $result['user'],
                'token' => $result['token'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function register(RegisterUserRequest $request)
    {
        $result = $this->service->register($request->validated());

        return response()->json([
            'message' => 'Utilisateur enregistré!',
            'user' => $result['user'],
            'token' => $result['token'],
        ], 201);
    }

    public function logout(Request $request)
    {
        $this->service->logout($request->user());

        return response()->json([
            'message' => 'Déconnexion réussie!',
        ]);
    }
}



/*
    Fortify installe automatiquement
        -  Les routes /login, /register, /logout grace à php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"
        - les actions d'authentification dans le dossier Actions
        
        */
