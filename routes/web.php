<?php

use App\Http\Controllers\BookingPlaceholderController;
use App\Http\Controllers\LandingController;
use App\Http\Middleware\SetSiteLocale;
use Illuminate\Support\Facades\Route;

Route::middleware(SetSiteLocale::class)->group(function () {
    Route::get('/', LandingController::class)->name('home');
    Route::get('/booking', BookingPlaceholderController::class)->name('booking');

    Route::prefix('en')->group(function () {
        Route::get('/', LandingController::class)->name('home.en')->defaults('locale', 'en');
        Route::get('/booking', BookingPlaceholderController::class)->name('booking.en')->defaults('locale', 'en');
    });
});
Route::inertia('playground', 'playground')->name('playground');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
