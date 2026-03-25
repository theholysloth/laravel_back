<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract ; 

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request){
         //on recupere les données validées par le form request  Fortify::authenticateUsing(...)

        $user = $request->user();//fortify a deja authentifié l'user
        $token = $user->createToken('api-token')->plainTextToken; //generation d'un Api token avec sanctum et valeur à envoyer au front S

        return response()->json([
            'message' => 'Connexion reussie ',
            'user' => $user, 
            'token' => $token
        ]);
    }
}
