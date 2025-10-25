<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\EmailPreviewController;

Route::get('/', [HomeController::class, 'index'])->name('main');
Route::get('/home', [HomeController::class, 'home'])->name('home');

// Email preview route (development only)
Route::get('/email-preview/verification', [EmailPreviewController::class, 'verificationEmail'])->name('email.preview.verification');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

// Serve storage files without symlink
Route::get('/storage/{path}', function ($path) {
    $file = storage_path('app/public/' . $path);

    if (!file_exists($file)) {
        abort(404);
    }

    return response()->file($file);
})->where('path', '.*')->withoutMiddleware(['auth']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
