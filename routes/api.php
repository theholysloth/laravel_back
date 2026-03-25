<?php

//use App\Http\Controllers\Api\TokenLogoutController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::post('/register', [AuthController::class, 'register']); //ces routes sont accessibles sans etre connecté
Route::post('/login', [AuthController::class, 'login']);

//Route::post('/logout', [AuthController::class, 'logout']);
//Route::apiResource('tasks', TaskController::class);

Route::middleware('auth:sanctum')->get('/me',function (Request $request){
    return response()->json([
        'user '=> $request->user(),
    ]);
});

Route::middleware('auth:sanctum')->group(function () {//pour acceder à ces routes, il faut etre authentifié par sanctum
    //Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('tasks', TaskController::class);


});
