<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract ; 


class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request){
        $user = $request->user();

        $token = $user->createToken('api-token')->plainTextToken; 

        return response()->json([
            'message' => 'Utilisateur enregistré!',
            'user' => $user, 
            'token' => $token
        ],201);
    }
}