<?php

namespace App\Http\Controllers;

use App\Models\Booking; // <-- ДОБАВИЛИ ЭТОТ use
use App\Models\Tour;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TourController extends Controller
{
    public function index()
    {
        return Inertia::render('Tours/Index', [
            'tours' => Tour::all(),
        ]);
    }

    public function show(Tour $tour)
    {
        return Inertia::render('Tours/Show', [
            'tour' => $tour,
        ]);
    }

    public function checkAvailability(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $bookedSeats = Booking::where('tour_id', $tour->id)
            ->where('booking_date', $validated['date'])
            ->whereIn('status', ['paid', 'reserved'])
            ->sum('seats');

        $availableSeats = $tour->max_seats - $bookedSeats;

        return response()->json([
            'available_seats' => $availableSeats,
        ]);
    }
}