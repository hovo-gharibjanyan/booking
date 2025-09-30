<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourDate extends Model
{
    use HasFactory;

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    protected $fillable = [
        'tour_id',
        'date',
    ];
}
