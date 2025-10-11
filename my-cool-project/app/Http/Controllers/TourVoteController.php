<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tour;

class TourVoteController extends Controller
{
    public function store(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'type' => 'required|in:like,dislike',
        ]);

        $user = $request->user();
        $voteType = $validated['type'];

        $existingVote = $user->votes()->where('tour_id', $tour->id)->first();

        DB::transaction(function () use ($user, $tour, $voteType, $existingVote) {
            if ($existingVote) {
                if ($existingVote->type === $voteType) {
                    $existingVote->delete();
                } else {
                    $existingVote->type = $voteType;
                    $existingVote->save();
                }
            } else {
                $user->votes()->create([
                    'tour_id' => $tour->id,
                    'type' => $voteType,
                ]);
            }

            $likes = $tour->votes()->where('type', 'like')->count();
            $dislikes = $tour->votes()->where('type', 'dislike')->count();

            $tour->update([
                'likes_count' => $likes,
                'dislikes_count' => $dislikes,
            ]);
        });

        return back();
    }
}
