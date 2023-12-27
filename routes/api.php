<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;


Route::post('/signup', [UserController::class, 'signup']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);
Route::post('/requestToJoinTeam', [UserController::class, 'requestToJoinTeam']);






Route::post('/createTeam', [TeamController::class, 'createTeam']);
Route::get('/getTeam/{id}', [TeamController::class, 'getTeam']);
Route::get('/getAllTeams', [TeamController::class, 'getAllTeams']);
Route::post('/addUserToTeam', [TeamController::class, 'addUserToTeam']);
Route::post('/requestToJoinChampionship', [TeamController::class, 'requestToJoinChampionship']);

Route::get('/getAllTeamRequests/{id}', [TeamRequestsController::class, 'getAllTeamRequests']);






Route::post('/createChampionship', [ChampionshipController::class, 'createChampionship']);
Route::post('/addTeamsToChampionship', [ChampionshipController::class, 'addTeamsToChampionship']);
Route::get('/getChampionship/{id}', [ChampionshipController::class, 'getChampionship']);
Route::get('/getAllChampionships', [ChampionshipController::class, 'getAllChampionships']);


Route::get('/getAllChampionshipRequests/{id}', [ChampionshipRequestsController::class, 'getAllChampionshipRequests']);








Route::post('/addIban', [IbanController::class, 'addIban']);
Route::get('/getAllIbans', [IbanController::class, 'getAllIbans']);
Route::get('/deleteIban/{id}', [IbanController::class, 'deleteIban']);





Route::middleware('auth:api')->group(function () {

    Route::get('protected-route', [UserController::class, 'protectedRoute']);
});

