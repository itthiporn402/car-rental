<?php
//ใช้สร้างข้อมูล รีวิวรถ แบบสุ่ม
namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use App\Models\Car;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'car_id' => Car::factory(),
            'booking_id' => Booking::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->paragraph,
        ];
    }
}
