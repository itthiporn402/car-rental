<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition()
    {
        $startDate = $this->faker->dateTimeBetween('now', '+2 months');
        $endDate = clone $startDate;
        $endDate->modify('+' . rand(1, 7) . ' days');

        return [
            'user_id' => User::factory(),
            'car_id' => Car::factory(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_amount' => $this->faker->numberBetween(1000, 10000),
            'status' => $this->faker->randomElement(['pending', 'approved', 'completed']),
            'notes' => $this->faker->optional()->sentence,
        ];
    }
}
