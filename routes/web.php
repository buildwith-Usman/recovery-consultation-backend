<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\EmailPreviewController;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

// Email preview route (development only)
Route::get('/email-preview/verification', [EmailPreviewController::class, 'verificationEmail'])->name('email.preview.verification');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
