<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value])->get('/user', action: function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthenticationController::class, 'login'])->name('auth.login');
    Route::post('register', [RegisterController::class, 'register'])->name('auth.register');

    Route::group(['middleware' => 'auth:sanctum', 'ability:' . TokenAbility::ISSUE_ACCESS_TOKEN->value], function () {
        Route::post('refresh_token', [AuthenticationController::class, 'refreshToken'])->name('auth.refreshToken');
    });
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('logout', [AuthenticationController::class, 'logout'])->name('auth.logout');
    });
});
