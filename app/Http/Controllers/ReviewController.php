<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        // ตรวจสอบข้อมูลที่ได้รับ
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'car_id' => 'required|exists:cars,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        // สร้างรีวิวใหม่
        $review = Review::create([
            'user_id' => auth()->id(), // หรือระบุผู้ใช้ที่กำลังล็อกอิน
            'booking_id' => $validated['booking_id'],
            'car_id' => $validated['car_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return response()->json(['message' => 'Review successfully submitted.']);
    }

    public function checkReview(Request $request)
{
    $validated = $request->validate([
        'booking_id' => 'required|exists:bookings,id',
        'car_id' => 'required|exists:cars,id',
    ]);

    // ตรวจสอบว่าเคยรีวิวหรือไม่
    $review = Review::where('booking_id', $request->booking_id)
                    ->where('car_id', $request->car_id)
                    ->first();

    return response()->json(['reviewed' => $review ? true : false]);
}

}
