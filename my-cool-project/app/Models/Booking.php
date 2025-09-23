<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [ // <-- ВОТ ЭТА ЧАСТЬ!
        'tour_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'booking_date',
        'seats',
        'total_minor',
        'status',
    ];
}
