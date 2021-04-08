<?php

use App\Http\Controllers\PassportController;
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

Route::post('/login', [PassportController::class, 'login']);
Route::post('/register', [PassportController::class, 'register']);
Route::post('/validate-account', [PassportController::class, 'validateRegistration']);

Route::middleware('auth:api')->group(function () {
  Route::post('/user', [UserController::class, 'updateUser']);
  Route::post('/changePassword', [UserController::class, 'updatePassword']);

  Route::get('/logout', [PassportController::class, 'logout']);
});
