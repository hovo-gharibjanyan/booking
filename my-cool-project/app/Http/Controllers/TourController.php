<?php

namespace App\Http\Controllers;

use App\Models\Booking; // <-- ДОБАВИЛИ ЭТОТ use
use App\Models\Tour;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TourController extends Controller
{
    // Этот метод у тебя уже есть
    public function index()
    {
        return Inertia::render('Tours/Index', [
            'tours' => Tour::all(),
        ]);
    }

    // И этот метод тоже уже есть
    public function show(Tour $tour)
    {
        return Inertia::render('Tours/Show', [
            'tour' => $tour,
        ]);
    }

    // --- НАЧАЛО НОВОГО КОДА ---
    // ДОБАВЬ ЭТОТ НОВЫЙ МЕТОД
    public function checkAvailability(Request $request, Tour $tour)
    {
        // 1. Валидация входящего параметра (даты)
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        // 2. Считаем забронированные места
        $bookedSeats = Booking::where('tour_id', $tour->id)
            ->where('booking_date', $validated['date'])
            ->whereIn('status', ['paid', 'reserved'])
            ->sum('seats');

        // 3. Вычисляем оставшиеся места
        $availableSeats = $tour->max_seats - $bookedSeats;

        // 4. Возвращаем чистый JSON-ответ
        return response()->json([
            'available_seats' => $availableSeats,
        ]);
    }
    // --- КОНЕЦ НОВОГО КОДА ---
}