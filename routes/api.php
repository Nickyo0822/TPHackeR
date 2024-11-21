<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FunctionHackerController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'api','prefix' => 'auth'], function ($router) {
    // Login
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('register', [AuthController::class, 'register']);

    // Profile
    Route::post('changeprofile', [ProfileController::class, 'changeprofile']);
    Route::post('changerights', [ProfileController::class, 'changerights']);

    // Utilisateur
    Route::post('me', [AuthController::class, 'me']);

    // Email
    Route::post('EmailVerificator', [FunctionHackerController::class, 'EmailVerificator']);
});