<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tour extends Model
{
    use HasFactory;

    public function host(): BelongsTo
    {
        return $this->belongsTo(Host::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(TourImage::class); 
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TourActivity::class);
    }

    public function dates(): HasMany
    {
        return $this->hasMany(TourDate::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function favoriteUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tour_user');
    }

    public function Votes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tour_votes');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    protected $fillable = [
        'title',
        'description',
        'price_minor',
        'max_seats',
        'host_id',
        'likes_count',
        'dislikes_count',
        'comments_count',
    ];
}