<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TourController;
use App\Http\Controllers\ChatbotController; 


Route::get('/tours/{tour}/availability', [TourController::class, 'checkAvailability'])
    ->name('tours.availability');


Route::post('/chat', [ChatbotController::class, 'handle']);