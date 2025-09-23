<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tour>
 */
class TourFactory extends Factory
{
    public function definition(): array
    {
        $availableDates = [];
        for ($i = 0; $i < 5; $i++) {
            $availableDates[] = now()->addDays(rand(10, 60))->toDateString();
        }
    
        return [
            'title' => 'Тур в ' . $this->faker->city,
            'description' => $this->faker->paragraph(3),
            'price_minor' => $this->faker->numberBetween(10000, 50000),
            'max_seats' => $this->faker->numberBetween(10, 30),
            'available_dates' => json_encode(array_unique($availableDates)),
        ];
    }
}
