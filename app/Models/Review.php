<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // ✅ ผู้ใช้ที่ให้คะแนน
        'booking_id',
        'car_id',
        'rating',
        'comment',
    ];

    //  รีวิว 1 รายการ เป็นของรถ 1 คัน
    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    //  รีวิว 1 รายการ เป็นของผู้ใช้ 1 คน
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //  รีวิว 1 รายการ เป็นของการจอง 1 รายการ
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
