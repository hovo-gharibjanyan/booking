<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TourVote extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'tour_id',
        'type',
    ];
}
