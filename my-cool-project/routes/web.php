<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TourController; 
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\FirebaseGoogleController; 
use App\Http\Controllers\FavoriteTourController;

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
Route::post('/auth/google/login', [FirebaseGoogleController::class, 'login'])->name('google.login');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store')->middleware('auth');
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/bookings/{booking}/voucher', [\App\Http\Controllers\BookingController::class, 'downloadVoucher'])
        ->name('bookings.voucher');
    
    Route::post('/bookings/{booking}/cancel', [\App\Http\Controllers\BookingController::class, 'cancel'])
        ->name('bookings.cancel');

    Route::get('/favorites', [FavoriteTourController::class, 'index'])->name('favorites.index');
    Route::post('/tours/{tour}/favorite', [FavoriteTourController::class, 'store'])->name('tours.favorite');
    Route::delete('/tours/{tour}/favorite', [FavoriteTourController::class, 'destroy'])->name('tours.unfavorite');
    Route::post('/tours/{tour}/vote', [\App\Http\Controllers\TourVoteController::class, 'store'])->name('tours.vote');
});
Route::get('/', [TourController::class, 'index'])->name('home');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/bookings/{booking}/payment', [\App\Http\Controllers\BookingController::class, 'showPaymentForm'])->name('bookings.payment.create');
    Route::post('/bookings/{booking}/payment', [\App\Http\Controllers\BookingController::class, 'processPayment'])->name('bookings.payment.store');
    Route::post('/bookings/{booking}/cancel', [\App\Http\Controllers\BookingController::class, 'cancelBooking'])->name('bookings.cancelBooking');
});

Route::get('/test-gemini', function () {
    try {
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) { return "Ошибка: Ключ GEMINI_API_KEY не найден в .env файле."; }

        $client = \Gemini::client($apiKey);
        
        $result = $client->generativeModel('gemini-pro-latest')
            ->generateContent('Напиши "Привет, Мир!" на русском');

        return $result->text();

    } catch (\Exception $e) {
        return "Критическая ошибка: " . $e->getMessage();
    }
});

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        Route::get('/bookings', [\App\Http\Controllers\Admin\BookingController::class, 'index'])
            ->name('bookings.index');

        Route::resource('tours', \App\Http\Controllers\Admin\TourController::class);
        Route::post('/bookings/{booking}/confirm-payment', [\App\Http\Controllers\Admin\BookingController::class, 'confirmPayment'])->name('bookings.confirm-payment');

    });

require __DIR__.'/auth.php';
