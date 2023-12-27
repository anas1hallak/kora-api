<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
//signup login 
Route::post('signup', [UserController::class, 'signup']);
Route::post('login', [UserController::class, 'login']);




Route::middleware('auth:api')->group(function () {
Route::get('protected-route', [UserController::class, 'protectedRoute']);
});

