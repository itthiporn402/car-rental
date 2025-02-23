<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Booking;

class ReviewSeeder extends Seeder
{
    public function run()
    {
        // ดึงข้อมูล Booking มาใช้
        $bookings = Booking::all();

        // ให้แต่ละ Booking มี 1 Review
        foreach ($bookings as $booking) {
            Review::factory()->create([
                'booking_id' => $booking->id,
            ]);
        }
    }
}
