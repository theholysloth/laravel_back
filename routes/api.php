<?php

//use App\Http\Controllers\Api\TokenLogoutController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\UserManagementController;



Route::post('/register', [AuthController::class, 'register']); //ces routes sont accessibles sans etre connecté
Route::post('/login', [AuthController::class, 'login']);

//Route::post('/logout', [AuthController::class, 'logout']);
//Route::apiResource('tasks', TaskController::class);


Route::middleware('auth:sanctum')->group(function () {//pour acceder à ces routes, il faut etre authentifié par sanctum
    Route::get('/me',function (Request $request){
        return response()->json([
            'user'=> $request->user()->load('roles'),
        ]);
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/users', [UserManagementController::class,'index']);
    Route::put('/users/{user}/role', [UserManagementController::class,'updateRole']);
    Route::get('/roles', [UserManagementController::class,'roles']);

    Route::post('/users', [CreateUserByAdminRequest::class,'store']);

    Route::apiResource('tasks', TaskController::class);



});
