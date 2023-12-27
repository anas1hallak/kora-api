<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
//signup login 
Route::post('signup', [UserController::class, 'signup']);
Route::post('login', [UserController::class, 'login']);
=======





Route::post('/signup', [UserController::class, 'signup']);
Route::post('/login', [UserController::class, 'login']);

Route::post('/createTeam', [TeamController::class, 'createTeam']);
Route::get('/getTeam/{id}', [TeamController::class, 'getTeam']);
Route::get('/getAllTeams', [TeamController::class, 'getAllTeams']);








>>>>>>> 76bbd4a765d3a181c551f9330166de157737eb51




Route::middleware('auth:api')->group(function () {
<<<<<<< HEAD
Route::get('protected-route', [UserController::class, 'protectedRoute']);
=======





    Route::get('protected-route', [UserController::class, 'protectedRoute']);
>>>>>>> 76bbd4a765d3a181c551f9330166de157737eb51
});

