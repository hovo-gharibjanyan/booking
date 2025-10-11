<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tour;
use Inertia\Inertia;

class FavoriteTourController extends Controller
{
    public function index(Request $request)
    {
        $favoriteTours = $request->user()
            ->favoriteTours()
            ->with('images')
            ->get()
            ->map(function ($tour) {
                $tour->is_favorited = true;
                return $tour;
            });

        return Inertia::render('Tours/Favorites', [
            'tours' => $favoriteTours,
        ]);
    }

    public function store(Request $request, Tour $tour)
    {
        $request->user()->favoriteTours()->syncWithoutDetaching([$tour->id]);
        return redirect()->back();
    }

    public function destroy(Request $request, Tour $tour)
    {
        $request->user()->favoriteTours()->detach($tour->id);
        return redirect()->back();
    }
}
