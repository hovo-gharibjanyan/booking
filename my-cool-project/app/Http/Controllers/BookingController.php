<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // --- 1. ВАЛИДАЦИЯ ---
    $validatedData = $request->validate([
        'tour_id' => 'required|exists:tours,id',
        'customer_name' => 'required|string|max:255',
        'customer_email' => 'required|email|max:255',
        'customer_phone' => 'required|string|max:20',
        'booking_date' => 'required|date',
        'seats' => 'required|integer|min:1',
    ]);

    $tour = Tour::findOrFail($validatedData['tour_id']);

    // --- 2. ПРОВЕРКА МЕСТ И СОЗДАНИЕ БРОНИ В ТРАНЗАКЦИИ ---
    DB::transaction(function () use ($validatedData, $tour) {
        // Считаем, сколько мест уже забронировано на эту дату
        $bookedSeats = Booking::where('tour_id', $tour->id)
            ->where('booking_date', $validatedData['booking_date'])
            ->whereIn('status', ['paid', 'reserved'])
            ->sum('seats');

        // Проверяем, хватает ли мест
        if (($bookedSeats + $validatedData['seats']) > $tour->max_seats) {
            // Если мест не хватает, "выбрасываем" ошибку валидации.
            throw ValidationException::withMessages([
                'seats' => 'Извините, на эту дату осталось только ' . ($tour->max_seats - $bookedSeats) . ' свободных мест.',
            ]);
        }

        // --- 3. СОЗДАНИЕ БРОНИ (РУЧНЫМ СПОСОБОМ, т.к. он надежнее) ---
        $booking = new Booking();
        $booking->tour_id = $tour->id;
        $booking->customer_name = $validatedData['customer_name'];
        $booking->customer_email = $validatedData['customer_email'];
        $booking->customer_phone = $validatedData['customer_phone'];
        $booking->booking_date = $validatedData['booking_date'];
        $booking->seats = $validatedData['seats'];
        $booking->total_minor = $tour->price_minor * $validatedData['seats'];
        $booking->status = 'reserved';
        $booking->save();
    });

    // --- 4. ВОЗВРАЩАЕМ ОТВЕТ ---
    return redirect()->route('tours.show', ['tour' => $tour->id])
        ->with('success', 'Тур успешно забронирован! Мы скоро с вами свяжемся.');
}
}