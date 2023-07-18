<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Chat;
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

// Route API Login and Register
Route::controller(UserController::class)->group(function(){
    Route::post( '/users/loginByEmail'    , 'loginEmail'      );
});

// Route API Sanctum Authentication
Route::middleware('auth:sanctum')->group( function () {
    Route::post( '/users/logout'          , [ UserController::class , 'logout'           ] );
    Route::get ( '/users/show/{id}'       , [ UserController::class , 'show'             ] );
    Route::post( '/users/updateUserData'  , [ UserController::class , 'updateUserData'   ] );
});