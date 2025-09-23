<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TourController;

Route::get('/tours/{tour}/availability', [TourController::class, 'checkAvailability'])
    ->name('tours.availability');