<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function(){
    p('working');
});


// Route::post('user/store', 'App\Http\Controllers\api\UserController@store');
Route::post('user/store', [UserController::class,'store']);
Route::get('users/get',[UserController::class,'index']);
Route::get('user/{id}',[UserController::class,'show']);
Route::delete('user/delete/{id}',[UserController::class,'destroy']);
Route::put('update/{id}',[UserController::class,'update']);
Route::patch('change-password/{id}',[UserController::class,'changePasssword']);