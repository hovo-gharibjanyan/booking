<?php

namespace App\Http\Controllers;

use App\Models\Booking; 
use App\Models\Tour;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\TourVoteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

class TourController extends Controller
{
    // public function index()
    // {
    //     $user = request()->user();
    //     $tours = Tour::with('images')->latest()->paginate(10);  

    //     if ($user) {
    //         $favoriteTourIds = $user->favoriteTours()->pluck('tours.id')->all();
    //         $tours->getCollection()->transform(function ($tour) use ($favoriteTourIds) {
    //             $tour->is_favorited = in_array($tour->id, $favoriteTourIds);
    //             return $tour;
    //         });
    //     }
    //     return Inertia::render('Tours/Index', [
    //         'tours' => $tours,
    //     ]);
    // }


    public function index(Request $request)
    {
        $user = $request->user();
        $toursPaginator = Tour::with('images')->latest()->paginate(10);

        if ($user) {
            $favoriteTourIds = $user->favoriteTours()->pluck('tours.id')->toArray();
            $userVotes = $user->votes()->pluck('type', 'tour_id');
            
            $toursPaginator->getCollection()->transform(function ($tour) use ($favoriteTourIds, $userVotes) {
                $tour->is_favorited = in_array($tour->id, $favoriteTourIds);
                $tour->user_vote = $userVotes->get($tour->id);
                return $tour;
            });
        }

        return Inertia::render('Tours/Index', [
            'tours' => $toursPaginator,
        ]);
    }

    public function show(Request $request, string $id)
    {
        $tour = Tour::with(['host', 'images', 'activities', 'dates'])->findOrFail($id);
        
        if ($user = $request->user()) {
            $tour->is_favorited = $user->favoriteTours()->where('tours.id', $tour->id)->exists();
            
            $vote = $user->votes()->where('tour_id', $tour->id)->first();
            $tour->user_vote = $vote ? $vote->type : null;
        }

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