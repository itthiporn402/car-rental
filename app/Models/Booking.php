<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'car_id',
        'start_date',
        'end_date',     // วันที่ที่กำหนดจะคืนรถ
        'returned_at',  // วันที่คืนรถจริง (ถ้ามี)
        'total_amount',
        'status',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'returned_at' => 'datetime',
        'status' => 'string'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // ความสัมพันธ์ไปยัง Car

    public static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            $exists = self::where('car_id', $booking->car_id)
                ->where('status', 'approved')
                ->where(function ($query) use ($booking) {
                    $query->whereBetween('start_date', [$booking->start_date, $booking->returned_at])
                          ->orWhereBetween('returned_at', [$booking->start_date, $booking->returned_at]);
                })
                ->exists();

            if ($exists) {
                throw new \Exception('รถคันนี้ถูกจองในช่วงเวลาที่เลือกแล้ว');
            }
        });
    }
}
