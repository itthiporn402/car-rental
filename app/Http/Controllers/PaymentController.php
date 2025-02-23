<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    // แสดงหน้าชำระเงิน
    public function show(Booking $booking)
    {
        return Inertia::render('Payments', [
            'booking' => $booking,
            'car' => $booking->car,
            'user' => auth()->user(),
        ]);
    }

    // บันทึกการชำระเงิน
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric',
            'payment_method' => 'required|string',
        ]);

        $payment = Payment::create([
            'booking_id' => $request->booking_id,
            'user_id' => $request->user_id,
            'transaction_id' => uniqid('txn_'),
            'amount' => $request->amount,
            'status' => 'paid',
            'payment_method' => $request->payment_method,
        ]);

        // อัปเดตสถานะการจอง
        $booking = Booking::findOrFail($request->booking_id);
        $booking->update(['status' => 'completed']); // ✅ ใช้ค่าที่ ENUM รองรับ

        return redirect()->route(route: 'rental.history')->with('success', 'ชำระเงินสำเร็จ!');
    }
}
