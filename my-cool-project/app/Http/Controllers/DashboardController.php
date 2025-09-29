<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $bookings = $user->bookings()->with('tour')->latest()->get();

        return Inertia::render('Dashboard', [
            'bookings' => $bookings
        ]);
    }
}