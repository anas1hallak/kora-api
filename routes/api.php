<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;


Route::post('/signup', [UserController::class, 'signup']);//tested
Route::post('/login', [UserController::class, 'login']);//tested
Route::get('/getUser/{id}', [UserController::class, 'getUser']);//tested
Route::get('/getAllUsers', [UserController::class, 'getAllUsers']);//tested
Route::delete('/deleteUser/{id}', [UserController::class, 'deleteUser']);
Route::put('/editUserSkills/{id}', [UserController::class, 'editUserSkills']);//testeda







Route::get('/getTeam/{id}', [TeamController::class, 'getTeam']);//tested
Route::get('/getTeamPlayers/{id}', [TeamController::class, 'getTeamPlayers']);//tested
Route::get('/getAllTeams', [TeamController::class, 'getAllTeams']);//tested
Route::post('/addUserToTeam/{id}', [TeamController::class, 'addUserToTeam']);//tested
Route::delete('/deleteTeam/{id}', [TeamController::class, 'deleteTeam']);//tested
Route::post('/requestToJoinChampionship', [TeamController::class, 'requestToJoinChampionship']);//tested
Route::put('/editTeamPoints/{id}', [TeamController::class, 'editTeamPoints']);//tested


Route::get('/getAllTeamRequests/{id}', [TeamRequestsController::class, 'getAllTeamRequests']);//tested


Route::get('/getFormation/{id}', [FormationController::class, 'getFormation']);//tested
Route::post('/editFormation', [FormationController::class, 'editFormation']);//tested





Route::post('/createChampionship', [ChampionshipController::class, 'createChampionship']);//tested
Route::post('/addTeamsToChampionship/{id}', [ChampionshipController::class, 'addTeamsToChampionship']);//tested
Route::post('/rejectChampionshipRequest/{id}', [ChampionshipController::class, 'rejectChampionshipRequest']);//tested
Route::get('/getChampionship/{id}', [ChampionshipController::class, 'getChampionship']);//tested
Route::get('/getAllChampionships', [ChampionshipController::class, 'getAllChampionships']);//tested



Route::get('/getTree/{id}', [RoundController::class, 'getTree']); //tested
Route::get('/getRoundMatchDetails/{id}', [RoundController::class, 'getRoundMatchDetails']); //tested


Route::get('/getGroups/{id}', [GroupController::class, 'getGroups']); //tested
Route::get('/getGroupMatches/{id}', [GroupController::class, 'getGroupMatches']); //tested


Route::put('/editRoundMatches/{id}', [RoundController::class, 'editRoundMatches']); //tested
Route::put('/editGroupMatches/{id}', [GroupController::class, 'editGroupMatches']); //tested



Route::get('/getAllChampionshipRequests/{id}', [ChampionshipRequestsController::class, 'getAllChampionshipRequests']);



// Route::post('/searchTeam', [SearchController::class, 'searchTeam']); //tested
// Route::post('/searchUser', [SearchController::class, 'searchUser']); //tested
// Route::post('/searchChampionship', [SearchController::class, 'searchChampionship']); //tested



Route::post('/addIban', [IbanController::class, 'addIban']); //tested
Route::get('/getAllIbans', [IbanController::class, 'getAllIbans']); //tested
Route::get('/deleteIban/{id}', [IbanController::class, 'deleteIban']);





Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/editProfile', [UserController::class, 'editProfile']);
    Route::post('/completeSignup', [UserController::class, 'completeSignup']);//tested
    Route::post('/requestToJoinTeam', [UserController::class, 'requestToJoinTeam']);//tested


    
    Route::post('/createTeam', [TeamController::class, 'createTeam']); //tested
    Route::get('/teamProfile', [TeamController::class, 'teamProfile']);
    Route::post('/editTeamProfile', [TeamController::class, 'editTeamProfile']);


    Route::get('/championshipProfile', [ChampionshipController::class, 'championshipProfile']);

    
    Route::post('/logout', [UserController::class, 'logout']);


});




Route::post('/sendNotification', [PushNotificationController::class, 'sendNotification']); //tested
