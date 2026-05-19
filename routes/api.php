<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/signup', [AuthController::class, 'createAccount']);
Route::post('/confirm-account', [AuthController::class, 'confirmAccount']);
Route::post('/resend-confirmation-code', [AuthController::class, 'resendConfirmationCode']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/confirm-forgot-password', [AuthController::class, 'confirmForgotPassword']);

Route::middleware(['cognito'])->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->attributes->get('user'));
    });

    Route::post('/refresh', [AuthController::class, 'refresh']);

    Route::post('/change-password', [AuthController::class, 'changePassword']);

    Route::get('/customers', [CustomerController::class, 'index']);

    Route::get('/customer/{cnpj}', [CustomerController::class, 'show']);

    Route::get('/products', [ProductController::class, 'index']);
});
