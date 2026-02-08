<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    //  แสดงหน้าชำระเงิน โดยส่งข้อมูลการจอง (booking), รถ (car), และผู้ใช้ (user) 
    public function show(Booking $booking)
    {
        return Inertia::render('Payments', [
            'booking' => $booking,
            'car' => $booking->car,
            'user' => auth()->user(),
        ]);
    }

    // บันทึกข้อมูลการชำระเงิน และอัปเดตสถานะการจองเป็น completed
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric',
            'payment_method' => 'required|string',
        ]);

        // สร้างข้อมูลการชำระเงิน และบันทึกลงฐานข้อมูล
        $payment = Payment::create([
            'booking_id' => $request->booking_id,
            'user_id' => $request->user_id,
            'transaction_id' => uniqid('txn_'),
            'amount' => $request->amount,
            'status' => 'completed',
            'payment_method' => $request->payment_method,
        ]);

        // อัปเดตสถานะการจอง
        $booking = Booking::findOrFail($request->booking_id);
        $booking->update(['status' => 'completed']); // ✅ ใช้ค่าที่ ENUM รองรับ

        return redirect()->route(route: 'rental.history')->with('success', 'ชำระเงินสำเร็จ!');
    }
}
