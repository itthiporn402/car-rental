<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\Car;

class BookingSeeder extends Seeder
{
    public function run()
    {
        // ดึง Users และ Cars มาใช้
        $users = User::all();
        $cars = Car::all();

        // สร้างการจอง 5 รายการ
        Booking::factory(5)->create([
            'user_id' => $users->random()->id,
            'car_id' => $cars->random()->id,
        ]);
    }
}
