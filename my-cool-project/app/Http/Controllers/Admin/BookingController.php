<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function index()
    {

        $bookings = Booking::with(['tour', 'user'])->latest()->get();

        return Inertia::render('Admin/Bookings/Index', [
            'bookings' => $bookings,
        ]);
    }
}
