<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
    
    Route::resource('thumbnails', \App\Http\Controllers\ThumbnailController::class)
        ->only(['index', 'store', 'show']);
        
    // API routes for real-time updates
    Route::prefix('api')->group(function () {
        Route::get('thumbnails/status', [\App\Http\Controllers\Api\ThumbnailController::class, 'status']);
        Route::get('thumbnails/{id}/status', [\App\Http\Controllers\Api\ThumbnailController::class, 'requestStatus']);
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
