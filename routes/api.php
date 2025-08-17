<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CuisineController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\InviteCodeController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\RedemptionController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\SupportTicketController;
use App\Http\Controllers\Api\VenueController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('auth/login', 'login');
    // ->middleware('throttle:5,1');
    // Max 5 login attempts per minute per IP
    Route::post('auth/register', 'register');
    // ->middleware('throttle:3,10');
    // Optional: Throttle register to prevent bot signups (3 per 10 mins)
    Route::delete('auth/logout', 'logout')->middleware('auth:sanctum');
    Route::get('auth/me', 'me')->middleware('auth:sanctum');
    Route::post('auth/forgot-password', 'forgotPassword');
    // ->middleware('throttle:3,10');
    // Max 3 OTP requests per 10 minutes
    Route::post('auth/verify-forgot-password-otp', 'verifyForgotPasswordOtp');
    // ->middleware('throttle:5,1');
    // Max 5 OTP verification attempts per minute
    Route::post('auth/reset-password', 'resetPassword');
    // ->middleware('throttle:3,10');
    // Max 3 password reset attempts per 10 minutes
    Route::post('auth/update-password', 'updatePassword')->middleware('auth:sanctum');
    Route::post('auth/update-profile', 'updateProfile')->middleware('auth:sanctum');
    Route::delete('auth/account', 'deleteAccount')->middleware('auth:sanctum');
});

// Public invite verification
Route::post('invite-codes/verify', [InviteCodeController::class, 'verify']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('devices', DeviceController::class)->except(['update']);

    Route::apiResource('cities', CityController::class)->only(['index']);
    Route::apiResource('offers', OfferController::class)->only(['index', 'show']);
    Route::apiResource('venues', VenueController::class)->only(['index']);
    Route::apiResource('cuisines', CuisineController::class)->only(['index']);
    Route::apiResource('support-tickets', SupportTicketController::class)->only(['store']);

    Route::controller(RestaurantController::class)->group(function () {
        Route::get('/restaurants', 'index');
        Route::get('/restaurants/by-city', 'restaurantsByCities');
        Route::get('/restaurants/{restaurant}', 'show');
    });

    Route::get('/restaurants/{restaurant}/redemptions/remaining', [RedemptionController::class, 'remainingRedemptions']);
    Route::post('/restaurants/{restaurant}/redeem', [RedemptionController::class, 'redeem']);

    // Invite routes (authenticated)
    Route::get('invite-codes', [InviteCodeController::class, 'index']); // list codes
    Route::post('invite-codes/{inviteCode}/send', [InviteCodeController::class, 'send']);
});
