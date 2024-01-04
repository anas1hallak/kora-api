<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;


Route::post('/signup', [UserController::class, 'signup']);//done
Route::post('/login', [UserController::class, 'login']);//done
Route::post('/logout', [UserController::class, 'logout']);
Route::get('/getAllUsers', [UserController::class, 'getAllUsers']);
Route::delete('/deleteUser/{id}', [UserController::class, 'deleteUser']);
Route::post('/requestToJoinTeam', [UserController::class, 'requestToJoinTeam']);






Route::post('/createTeam', [TeamController::class, 'createTeam']); //done
Route::get('/getTeam/{id}', [TeamController::class, 'getTeam']);//done
Route::get('/getTeamPlayers/{id}', [TeamController::class, 'getTeamPlayers']);//done
Route::get('/getAllTeams', [TeamController::class, 'getAllTeams']);//done
Route::post('/addUserToTeam/{id}', [TeamController::class, 'addUserToTeam']);//done
Route::delete('/deleteTeam/{id}', [TeamController::class, 'deleteTeam']);//done
Route::post('/requestToJoinChampionship', [TeamController::class, 'requestToJoinChampionship']);

Route::get('/getAllTeamRequests/{id}', [TeamRequestsController::class, 'getAllTeamRequests']);






Route::post('/createChampionship', [ChampionshipController::class, 'createChampionship']);//done
Route::post('/addTeamsToChampionship/{id}', [ChampionshipController::class, 'addTeamsToChampionship']);//done
Route::get('/getChampionship/{id}', [ChampionshipController::class, 'getChampionship']);//done
Route::get('/getAllChampionships', [ChampionshipController::class, 'getAllChampionships']);//done



Route::post('/createTree/{id}', [ChampionshipController::class, 'createTree']);
Route::get('/getTree/{id}', [ChampionshipController::class, 'getTree']);



Route::get('/getAllChampionshipRequests/{id}', [ChampionshipRequestsController::class, 'getAllChampionshipRequests']);








Route::post('/addIban', [IbanController::class, 'addIban']);
Route::get('/getAllIbans', [IbanController::class, 'getAllIbans']);
Route::get('/deleteIban/{id}', [IbanController::class, 'deleteIban']);





Route::middleware('auth:api')->group(function () {

    Route::get('protected-route', [UserController::class, 'protectedRoute']);
});

