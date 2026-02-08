<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'year',
        'price_per_day',
        'status',
        'image',
        'color'
    ];

    //ตรวจสอบว่าไฟล์รูปภาพ มีอยู่จริงใน Storage หรือไม่
    public function getImagePathAttribute()
    {
        return $this->image && Storage::disk('public')->exists($this->image)
            ? asset("storage/" . $this->image)
            : asset('cars/default.jpg');
    }

    // รถ 1 คัน สามารถถูกจองได้หลายครั้ง (hasMany) เชื่อมกับ ตาราง bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // รถ 1 คัน สามารถมีรีวิวหลายรีวิว (hasMany) เชื่อมกับ ตาราง reviews
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
