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
        'start_date',   //วันที่เริ่มเช่ารถ
        'end_date',     // วันที่ที่กำหนดจะคืนรถ
        'returned_at',  // วันที่คืนรถ
        'total_amount',
        'status',
        'notes'
    ];

    // แปลงประเภทข้อมูลอัตโนมัติ
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'returned_at' => 'datetime',
        'status' => 'string'
    ];

    // จองโดยผู้ใช้ 1 คน (BelongsTo) เชื่อมกับ ตาราง users
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // แต่ละการจองเป็นของรถ 1 คัน (BelongsTo)
    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    // แต่ละการจองสามารถมีรีวิวได้ 1 รีวิว (hasOne) เชื่อมกับ ตาราง reviews
    public function review()
    {
        return $this->hasOne(Review::class);
    }

    // แต่ละการจองสามารถมีการชำระเงินได้ 1 การชำระเงิน (hasOne) เชื่อมกับ ตาราง payments
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // ตรวจสอบว่ารถถูกจองในช่วงเวลาที่เลือกหรือไม่
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
