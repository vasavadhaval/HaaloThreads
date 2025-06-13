<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OtpAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\API\FamilyMemberController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\Auth\RegisterController;

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

// Public routes
Route::post('/otp/request', [OtpAuthController::class, 'requestOtp']);
Route::post('/otp/verify', [OtpAuthController::class, 'verifyOtp']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/register/verify', [RegisterController::class, 'verifyOtpAndCompleteRegistration']);
Route::post('/auth/resend-otp', [RegisterController::class, 'resendOtp']);
Route::post('/auth/login/resend-otp', [LoginController::class, 'resendOtp']);

Route::prefix('auth')->group(function () {
    // OTP Login
    Route::post('/login/request-otp', [LoginController::class, 'requestOtp']);
    Route::post('/login/verify-otp', [LoginController::class, 'verifyOtp']);

    // Password Login (optional)
    Route::post('/login/password', [LoginController::class, 'passwordLogin']);
});

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);


    Route::group(['prefix' => 'family-members'], function () {
        Route::post('/', [FamilyMemberController::class, 'store']);
        Route::post('/multiple', [FamilyMemberController::class, 'storeMultiple']);
        // You can add other family member related routes here (e.g., update, destroy, index, show)
        Route::get('/', [FamilyMemberController::class, 'index']);
        // Route::get('/{familyMember}', [FamilyMemberController::class, 'show']);
        // Route::put('/{familyMember}', [FamilyMemberController::class, 'update']);
        // Route::delete('/{familyMember}', [FamilyMemberController::class, 'destroy']);
    });


});
