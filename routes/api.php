<?php

use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes
Route::post('/login', [ApiAuthController::class, 'login']);


// Protected Routes
Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::post('/logout', [ApiAuthController::class, 'logout']);

    Route::get('/clients/search/{name}', [ClientController::class, 'search']);
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('users', UserController::class);
});
