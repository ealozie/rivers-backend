<?php

use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\LogoutController;
use App\Http\Controllers\API\TicketBulkVendingController;
use App\Http\Controllers\API\TicketEnforcementController;
use App\Http\Controllers\API\TicketVendingController;
use App\Http\Controllers\API\WalletFundTransferController;
use App\Http\Controllers\TicketAgentCategoryController;
use App\Http\Controllers\TicketCategoryController;
use Illuminate\Support\Facades\Route;

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


Route::prefix('v1')->group(function () {
    Route::post('login', LoginController::class);
    Route::post('logout', LogoutController::class)->middleware('auth:sanctum');
    Route::apiResource('forgot-password', ForgotPasswordController::class)->only(['store', 'update']);
    Route::apiResource('ticket-vending', TicketVendingController::class)->middleware('auth:sanctum')->only(['index', 'store', 'show']);
    Route::apiResource('ticket-bulk-vending', TicketBulkVendingController::class)->middleware('auth:sanctum')->only(['store', 'show', 'index']);
    Route::post('ticket-enforcement', TicketEnforcementController::class)->middleware('auth:sanctum');
    Route::get('ticket-categories', TicketCategoryController::class);
    Route::get('ticket-agent-categories', TicketAgentCategoryController::class)->middleware('auth:sanctum');
    Route::post('wallet-fund-transfer', WalletFundTransferController::class)->middleware('auth:sanctum');
    //Route::apiResource('users', UserController::class);
});
