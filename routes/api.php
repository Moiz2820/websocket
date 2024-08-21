<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;



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
Route::middleware('auth:api')->post('/broadcasting/auth', function (Request $request) {
    if (!$request->user()) {
        Log::info('Broadcast Auth Failed: User not authenticated');
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    Log::info('Broadcast Auth Success: User authenticated', ['user_id' => $request->user()->id]);
    return Broadcast::auth($request);
});


Route::middleware('auth:api')->get('/verify', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:api')->group(function(){
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::controller(MessageController::class)->group(function(){
     Route::get('/users','users');
     Route::get('/messages/{user}','messages');
     Route::post('/sendmessage','sendMessage');
    });
});
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
