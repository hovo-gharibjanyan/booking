<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TourController; 
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/', [TourController::class, 'index']);
Route::get('/tours/{tour}', [TourController::class, 'show'])->name('tours.show');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store')->middleware('auth');
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/bookings/{booking}/voucher', [\App\Http\Controllers\BookingController::class, 'downloadVoucher'])
        ->name('bookings.voucher');
    
    Route::post('/bookings/{booking}/cancel', [\App\Http\Controllers\BookingController::class, 'cancel'])
        ->name('bookings.cancel');
});
Route::get('/', [TourController::class, 'index'])->name('home');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        Route::get('/bookings', [\App\Http\Controllers\Admin\BookingController::class, 'index'])
            ->name('bookings.index');

        Route::resource('tours', \App\Http\Controllers\Admin\TourController::class);
        
    });

require __DIR__.'/auth.php';
