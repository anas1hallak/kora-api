<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;


Route::post('/signup', [UserController::class, 'signup']);//tested
Route::post('/login', [UserController::class, 'login']);//tested
Route::post('/logout', [UserController::class, 'logout']);
Route::post('/completeSignup', [UserController::class, 'completeSignup']);//tested
Route::get('/profile/{id}', [UserController::class, 'profile']);
Route::get('/getAllUsers', [UserController::class, 'getAllUsers']);//tested
Route::delete('/deleteUser/{id}', [UserController::class, 'deleteUser']);
Route::post('/requestToJoinTeam', [UserController::class, 'requestToJoinTeam']);//tested






Route::post('/createTeam', [TeamController::class, 'createTeam']); //tested
Route::get('/getTeam/{id}', [TeamController::class, 'getTeam']);//tested
Route::get('/getTeamPlayers/{id}', [TeamController::class, 'getTeamPlayers']);//tested
Route::get('/getAllTeams', [TeamController::class, 'getAllTeams']);//tested
Route::post('/addUserToTeam/{id}', [TeamController::class, 'addUserToTeam']);//tested
Route::delete('/deleteTeam/{id}', [TeamController::class, 'deleteTeam']);//tested
Route::post('/requestToJoinChampionship', [TeamController::class, 'requestToJoinChampionship']);//tested

Route::get('/getAllTeamRequests/{id}', [TeamRequestsController::class, 'getAllTeamRequests']);//tested






Route::post('/createChampionship', [ChampionshipController::class, 'createChampionship']);//tested
Route::post('/addTeamsToChampionship/{id}', [ChampionshipController::class, 'addTeamsToChampionship']);//tested
Route::get('/getChampionship/{id}', [ChampionshipController::class, 'getChampionship']);//tested
Route::get('/getAllChampionships', [ChampionshipController::class, 'getAllChampionships']);//tested



////Route::post('/createTree/{id}', [ChampionshipController::class, 'createTree']);
Route::get('/getTree/{id}', [RoundController::class, 'getTree']); //tested
Route::get('/getGroups/{id}', [GroupController::class, 'getGroups']); //tested
Route::get('/getGroupMatches/{id}', [GroupController::class, 'getGroupMatches']); //tested






Route::get('/getAllChampionshipRequests/{id}', [ChampionshipRequestsController::class, 'getAllChampionshipRequests']);








Route::post('/addIban', [IbanController::class, 'addIban']); //tested
Route::get('/getAllIbans', [IbanController::class, 'getAllIbans']); //tested
Route::get('/deleteIban/{id}', [IbanController::class, 'deleteIban']);





Route::middleware('auth:api')->group(function () {

    Route::get('protected-route', [UserController::class, 'protectedRoute']);
});

