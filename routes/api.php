<?php

use App\Http\Controllers\CardsController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
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

Route::middleware('checkDBConnection')->group(function() {
    Route::put('register', [UsersController::class, 'register']);
    Route::put('login', [UsersController::class, 'login']);
    Route::put('recover', [UsersController::class, 'recover']);
    Route::put('changePassword', [UsersController::class, 'changePassword']);

    Route::middleware(['api-auth', 'admin-auth'])->group(function() {
        Route::prefix('cards')->group(function() {
            Route::put('create', [CardsController::class, 'create']);
        });
        Route::prefix('collections')->group(function() {
            Route::put('create', [CollectionsController::class, 'create']);
        });
    });
});
