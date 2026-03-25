<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TokenLoginController extends Controller
{
    //
    public function store(Request $request){
        $request->user()->currentAccessToken()->delete();//supprimer le token
        
        return response()->json([
            'message' => 'deconnexion reussie!',
        ]);
    }
}
