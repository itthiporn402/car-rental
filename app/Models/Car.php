<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Car extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'brand', 'year', 'price_per_day', 'status', 'image', 'color'];

    // ✅ แก้ให้ใช้ `storage` ที่ถูกต้อง
    public function getImagePathAttribute()
    {
        return $this->image && Storage::disk('public')->exists($this->image)
            ? asset("storage/" . $this->image)
            : asset('cars/default.jpg');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
