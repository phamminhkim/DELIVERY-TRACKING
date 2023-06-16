<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
// Google Sign In
Route::post('/get-google-sign-in-url', [\App\Http\Controllers\Api\GoogleController::class, 'getGoogleSignInUrl']);
Route::get('/callback', [\App\Http\Controllers\Api\GoogleController::class, 'loginCallback']);

//api order
Route::get('order', [OrderController::class, 'index']);
Route::post('order_store', [OrderController::class, 'store']);
Route::put('order_put/{id}', [OrderController::class, 'update']);
Route::delete('order_delete/{id}', [OrderController::class, 'destroy']);


