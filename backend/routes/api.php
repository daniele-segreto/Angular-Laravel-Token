<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
    'middleware' => 'api', // Definizione del middleware 'api' per il gruppo di route
    'prefix' => 'auth' // Definizione del prefisso 'auth' per le route all'interno del gruppo
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']); // Route POST per l'endpoint di login gestito dal metodo 'login' dell'AuthController
    Route::post('/register', [AuthController::class, 'register']); // Route POST per l'endpoint di registrazione gestito dal metodo 'register' dell'AuthController
    Route::post('/logout', [AuthController::class, 'logout']); // Route POST per l'endpoint di logout gestito dal metodo 'logout' dell'AuthController
    Route::post('/refresh', [AuthController::class, 'refresh']); // Route POST per l'endpoint di refresh del token gestito dal metodo 'refresh' dell'AuthController
    Route::get('/user-profile', [AuthController::class, 'userProfile']); // Route GET per l'endpoint del profilo utente gestito dal metodo 'userProfile' dell'AuthController
});

// Route::group([
//     'middleware' => 'api',
//     'prefix' => 'auth'

// ], function ($router) {
//     Route::post('login', 'AuthController@login');
//     Route::post('register', 'AuthController@register');
//     Route::post('logout', 'AuthController@logout');
//     Route::post('refresh', 'AuthController@refresh');
//     Route::get('user-profile', 'AuthController@userProfile');
// });
