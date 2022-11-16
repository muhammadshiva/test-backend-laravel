<?php

use App\Http\Controllers\API\OperatorCardController;
use App\Http\Controllers\API\PaymentMethodController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\TipController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\WalletController;
use App\Http\Controllers\API\MoneyPlanController;
use App\Http\Controllers\Api\v1\AuthController;
use App\Models\MoneyPlan;
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
Route::post('callback_midtrans', [TransactionController::class, 'callback']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [UserController::class, 'fetch']);
    Route::post('user', [UserController::class, 'updateProfile']);
    Route::get('user/{username}', [UserController::class, 'getUserByUserName']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('is-email-exist', [UserController::class, 'isEmailExist']);
    Route::get('wallets', [WalletController::class, 'fetch']);
    Route::put('wallets', [WalletController::class, 'updatePin']);
    Route::post('top_ups', [TransactionController::class, 'topUp']);
    Route::post('transfers', [TransactionController::class, 'transfer']);
    Route::get('transactions', [TransactionController::class, 'getTransactions']);
    Route::get('transfer_histories', [TransactionController::class, 'getTransferHistories']);
    Route::get('operator_cards', [OperatorCardController::class, 'fetch']);
    Route::post('data_plans', [TransactionController::class, 'dataPlans']);
    Route::get('money_plans', [MoneyPlanController::class, 'fetch']);
    Route::post('money_plans', [MoneyPlanController::class, 'create']);
});

// V1

Route::prefix("v1")->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('user', [AuthController::class, 'updateProfile']);
        Route::post('is-email-exist', [AuthController::class, 'isEmailExist']);
        Route::get('user', [AuthController::class, 'getUser']);
        Route::get('user/{username}', [AuthController::class, 'getUserByUserName']);
        Route::get('products', [ProductController::class, 'all']);

        Route::get('tips', [TipController::class, 'fetch']);
        Route::get('payment_methods', [PaymentMethodController::class, 'all']);
        Route::get('wallets', [WalletController::class, 'fetch']);
        Route::put('wallets', [WalletController::class, 'updatePin']);
        Route::post('top_ups', [TransactionController::class, 'topUp']);
        Route::post('transfers', [TransactionController::class, 'transfer']);
        Route::get('transactions', [TransactionController::class, 'getTransactions']);
        Route::get('transfer_histories', [TransactionController::class, 'getTransferHistories']);
        Route::get('operator_cards', [OperatorCardController::class, 'fetch']);
        Route::post('data_plans', [TransactionController::class, 'dataPlans']);
        Route::get('money_plans', [MoneyPlanController::class, 'fetch']);
        Route::post('money_plans', [MoneyPlanController::class, 'create']);
    });
});