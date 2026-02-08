<?php
// ใช้สร้างข้อมูล การจองรถ แบบสุ่ม
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
            'user_id' => User::factory(), // สร้างข้อมูลผู้ใช้แบบสุ่ม
            'car_id' => Car::factory(), // สร้างข้อมูลรถแบบสุ่ม
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_amount' => $this->faker->numberBetween(1000, 10000),
            'status' => $this->faker->randomElement(['pending', 'approved', 'completed']), // สถานะการจอง
            'notes' => $this->faker->optional()->sentence, // หมายเหตุ
        ];
    }
}
