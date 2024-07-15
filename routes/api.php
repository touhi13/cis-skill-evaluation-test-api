<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SubscriptionController;
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

    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Subscription routes
    Route::middleware(['token.verify'])->group(function () {
        Route::post('/create-checkout-session', [SubscriptionController::class, 'createCheckoutSession']);
        Route::post('/generate-token', [AuthController::class, 'generateToken']);
        Route::get('/report', [SubscriptionController::class, 'monthlyPaymentReport']);

    });
    Route::post('/stripe/webhook', [SubscriptionController::class, 'handleWebhook']);

});
