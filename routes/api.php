<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;


Route::post('/signup', [UserController::class, 'signup']);
Route::post('/login', [UserController::class, 'login']);

Route::post('/createTeam', [TeamController::class, 'createTeam']);
Route::get('/getTeam/{id}', [TeamController::class, 'getTeam']);
Route::get('/getAllTeams', [TeamController::class, 'getAllTeams']);




Route::middleware('auth:api')->group(function () {





    Route::get('protected-route', [UserController::class, 'protectedRoute']);
});

