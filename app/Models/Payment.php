<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', // ✅ เพิ่ม booking_id ที่นี่
        'user_id',
        'amount',
        'status',
        'payment_method',
        'transaction_id',
        'paid_at',
    ];
}
