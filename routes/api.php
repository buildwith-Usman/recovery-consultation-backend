<?php

use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\FileUploadController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\AdBannerController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\UserFavoriteController;

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

    Route::post('/upload-file', [FileUploadController::class, 'uplaod'])->name('file.uplaod');
    Route::get('/file', [FileUploadController::class, 'file'])->name('file');

    // Public endpoint for active ad banners
    Route::get('/ad-banners/active', [AdBannerController::class, 'activeList'])->name('ad-banners.active');

    // Public endpoints for products
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/category/{categoryId}', [ProductController::class, 'byCategory'])->name('products.by-category');

    // Public endpoint for active categories (for dropdown)
    Route::get('/categories/active', [CategoryController::class, 'activeList'])->name('categories.active');

    // Protected routes
    Route::middleware(['auth:api'])->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/user', [UserController::class, 'index'])->name('user');
        Route::post('/update-profile', [UserController::class, 'update_profile'])->name('user.update_profile');
        Route::post('add-questionnaires', [UserController::class, 'add_questionnaires'])->name('questionnaires.add');

        // Patient's endpoints
        Route::get('match-doctors-list', [PatientController::class, 'match_doctors_list'])->name('patient.match.doctors.list');
        Route::get('doctors-list', [PatientController::class, 'doctors_list'])->name('patient.doctors.list');
        Route::post('appointment-booking', [PatientController::class, 'appointment_booking'])->name('patient.appointment.booking');
        Route::post('appointment-update', [PatientController::class, 'appointment_update'])->name('patient.appointment.update');
        Route::post('add-reviews', [PatientController::class, 'add_reviews'])->name('patient.reviews.add');

        // Doctor's endpoints
        Route::get('patients', [DoctorController::class, 'patients'])->name('doctor.patients');
        Route::get('patient-history', [DoctorController::class, 'patient_history'])->name('doctor.patient.history');

        // For Users (Patients and Doctors) endpoints
        Route::get('user-detail', [UserController::class, 'user_details'])->name('user.details');
        // Route::post('appointments-list', [UserController::class, 'appointments'])->name('appointments');
        Route::get('reviews', [UserController::class, 'reviews'])->name('reviews');
        Route::get('appointments', [UserController::class, 'appointments']);
        Route::get('appointment-detail', [UserController::class, 'appointment_detail'])->name('appointment.detail');

        // Patient Prescriptions
        Route::get('prescriptions', [UserController::class, 'prescriptions'])->name('prescriptions.list');
        Route::get('prescription-detail', [UserController::class, 'prescription_detail'])->name('prescription.detail');

        // Doctor Prescriptions
        Route::prefix('doctor')->group(function () {
            Route::get('prescriptions', [\App\Http\Controllers\Api\Doctor\PrescriptionController::class, 'index']);
            Route::post('prescriptions', [\App\Http\Controllers\Api\Doctor\PrescriptionController::class, 'store']);
            Route::get('prescriptions/{id}', [\App\Http\Controllers\Api\Doctor\PrescriptionController::class, 'show']);
            Route::put('prescriptions/{id}', [\App\Http\Controllers\Api\Doctor\PrescriptionController::class, 'update']);
            Route::delete('prescriptions/{id}', [\App\Http\Controllers\Api\Doctor\PrescriptionController::class, 'destroy']);
        });

        // Order Management
        Route::get('orders', [\App\Http\Controllers\Api\OrderController::class, 'index'])->name('orders.list');
        Route::post('orders', [\App\Http\Controllers\Api\OrderController::class, 'store'])->name('orders.create');
        Route::get('orders/{orderNumber}', [\App\Http\Controllers\Api\OrderController::class, 'show'])->name('orders.show');
        Route::get('orders/{orderNumber}/track', [\App\Http\Controllers\Api\OrderController::class, 'track'])->name('orders.track');

        // User Favorite Products
        Route::get('favorites', [UserFavoriteController::class, 'index'])->name('favorites.index');
        Route::post('favorites/{productId}', [UserFavoriteController::class, 'store'])->name('favorites.store');
        Route::delete('favorites/{productId}', [UserFavoriteController::class, 'destroy'])->name('favorites.destroy');
        Route::get('favorites/{productId}/check', [UserFavoriteController::class, 'check'])->name('favorites.check');

        // Admin endpoints
        Route::middleware(['auth.admin'])->prefix('admin')->group(function () {
            Route::post('update-user', [AdminUserController::class, 'update_user']);
            Route::get('users-list', [AdminUserController::class, 'allUsers']);
            Route::post('approve', [AdminUserController::class, 'approve']);

            // Ad Banner CRUD endpoints
            Route::get('ad-banners', [AdBannerController::class, 'index']);
            Route::post('ad-banners', [AdBannerController::class, 'store']);
            Route::get('ad-banners/{id}', [AdBannerController::class, 'show']);
            Route::put('ad-banners/{id}', [AdBannerController::class, 'update']);
            Route::delete('ad-banners/{id}', [AdBannerController::class, 'destroy']);

            // Category CRUD endpoints
            Route::get('categories', [CategoryController::class, 'index']);
            Route::post('categories', [CategoryController::class, 'store']);
            Route::get('categories/{id}', [CategoryController::class, 'show']);
            Route::put('categories/{id}', [CategoryController::class, 'update']);
            Route::delete('categories/{id}', [CategoryController::class, 'destroy']);

            // Product CRUD endpoints
            Route::get('products', [AdminProductController::class, 'index']);
            Route::post('products', [AdminProductController::class, 'store']);
            Route::get('products/{id}', [AdminProductController::class, 'show']);
            Route::put('products/{id}', [AdminProductController::class, 'update']);
            Route::delete('products/{id}', [AdminProductController::class, 'destroy']);

            // Order Management endpoints
            Route::get('orders', [\App\Http\Controllers\Api\Admin\OrderController::class, 'index']);
            Route::get('orders/{orderNumber}', [\App\Http\Controllers\Api\Admin\OrderController::class, 'show']);
            Route::put('orders/{orderNumber}/status', [\App\Http\Controllers\Api\Admin\OrderController::class, 'updateStatus']);
        });
    });
});