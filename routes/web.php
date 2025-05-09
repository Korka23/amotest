<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;



Route::group(['as' =>'auth.', 'prefix' => 'auth'], function () {
   Route::get('redirect', [AuthController::class, 'redirect']);
   Route::get('callback', [AuthController::class, 'callback']);
});
Route::post('webhook', [WebhookController::class, 'handle']);
Route::get('test-access-token', [AuthController::class, 'accessToken']);

Route::get('/', function () {
    return view('welcome');
});
