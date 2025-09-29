<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Seeder;

class TourSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Tour::factory()->count(50)->create();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
