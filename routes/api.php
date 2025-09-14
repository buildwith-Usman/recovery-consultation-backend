<?php

use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\HealthController;

// add prefix name for api

Route::name('api.')->group(function () {

    Route::get('/', function () {
        return response()->json(['message' => 'Recovery App API is running']);
    });

    // Health check endpoints
    Route::get('/health', [HealthController::class, 'check'])->name('health');
    Route::get('/health/email-stats', [HealthController::class, 'emailStats'])->name('health.email-stats');

    // Public routes
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/register', [RegisterController::class, 'register'])->name('register');
    
    // Email verification routes (public) with rate limiting
    Route::middleware(['email.rate.limit'])->group(function () {
        Route::post('/email/verify', [EmailVerificationController::class, 'verify'])->name('email.verify');
        Route::post('/email/resend', [EmailVerificationController::class, 'resend'])->name('email.resend');
    });
    
    // Forgot password routes
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetCode'])->name('forgot-password');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('reset-password');

    // Protected routes
    Route::middleware(['auth:api'])->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/user', [UserController::class, 'index'])->name('user');
        Route::post('/update-profile', [UserController::class, 'update_profile'])->name('user.update_profile');
        Route::post('add-questionnaires', [UserController::class, 'add_questionnaires'])->name('questionnaires.add');

        // Patient's doctors endpoints
        Route::get('match-doctors-list', [UserController::class, 'match_doctors_list'])->name('match.doctors.list');
        Route::get('doctors-list', [PatientController::class, 'doctors_list'])->name('doctors.list');
        Route::get('doctor', [PatientController::class, 'doctor_details'])->name('doctor.details');

        // Admin endpoints
        Route::prefix('admin')->group(function () {
            Route::get('users-list', [AdminUserController::class, 'allUsers']);
            Route::post('approve', [AdminUserController::class, 'approve']);
        });
    });
});