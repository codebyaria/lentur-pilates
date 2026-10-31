<?php

use App\Http\Controllers\BookingCancellationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\LandingController;
use App\Http\Middleware\SetSiteLocale;
use Illuminate\Support\Facades\Route;

Route::middleware(SetSiteLocale::class)->group(function () {
    Route::get('/', LandingController::class)->name('home');
    Route::get('/booking', [BookingController::class, 'create'])->name('booking');
    Route::post('/booking', [BookingController::class, 'store'])->middleware('throttle:10,1')->name('booking.store');
    Route::get('/booking/selesai/{booking:code}', [BookingController::class, 'confirmed'])->middleware('signed')->name('booking.confirmed');
    Route::get('/booking/{booking:code}/batal', [BookingCancellationController::class, 'show'])->middleware('signed')->name('booking.cancel');
    Route::post('/booking/{booking:code}/batal', [BookingCancellationController::class, 'store'])->middleware(['signed', 'throttle:10,1'])->name('booking.cancel.store');
    Route::get('/booking/{booking:code}/kalender.ics', [BookingController::class, 'calendar'])->middleware('signed')->name('booking.calendar');

    Route::prefix('en')->group(function () {
        Route::get('/', LandingController::class)->name('home.en')->defaults('locale', 'en');
        Route::get('/booking', [BookingController::class, 'create'])->name('booking.en')->defaults('locale', 'en');
        Route::post('/booking', [BookingController::class, 'store'])->middleware('throttle:10,1')->name('booking.store.en')->defaults('locale', 'en');
        Route::get('/booking/done/{booking:code}', [BookingController::class, 'confirmed'])->middleware('signed')->name('booking.confirmed.en')->defaults('locale', 'en');
        Route::get('/booking/{booking:code}/cancel', [BookingCancellationController::class, 'show'])->middleware('signed')->name('booking.cancel.en')->defaults('locale', 'en');
        Route::post('/booking/{booking:code}/cancel', [BookingCancellationController::class, 'store'])->middleware(['signed', 'throttle:10,1'])->name('booking.cancel.store.en')->defaults('locale', 'en');
        Route::get('/booking/{booking:code}/calendar.ics', [BookingController::class, 'calendar'])->middleware('signed')->name('booking.calendar.en')->defaults('locale', 'en');
    });
});

Route::inertia('playground', 'playground')->name('playground');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
