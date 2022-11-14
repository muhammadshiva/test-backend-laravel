<?php

use App\Http\Controllers\API\PaymentMethodController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\TipController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\WalletController;
use App\Models\Wallet;
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



Route::get('products', [ProductController::class, 'all']);

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::get('tips', [TipController::class, 'fetch']);
Route::get('payment_methods', [PaymentMethodController::class, 'all']);

Route::get('user/{username}', [UserController::class, 'getUserByUserName']);



Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [UserController::class, 'fetch']);
    Route::post('user', [UserController::class, 'updateProfile']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('is-email-exist', [UserController::class, 'isEmailExist']);
    Route::get('wallets', [WalletController::class, 'fetch']);
    Route::post('wallets', [WalletController::class, 'updatePin']);
});
