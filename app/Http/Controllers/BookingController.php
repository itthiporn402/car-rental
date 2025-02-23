<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Car;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Inertia\Inertia;


class BookingController extends Controller
{
    /**
     * แสดงรายการจองทั้งหมด
     */
    public function index()
    {
        $bookings = Booking::with('car', 'user')->latest()->get();

        return inertia('Bookings/Index', [
            'bookings' => $bookings->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'car' => [
                        'id' => $booking->car->id,  // ดึง car.id
                        'name' => $booking->car->brand . ' ' . $booking->car->name,
                        'image' => $booking->car->image ?? null,
                    ],
                    'user' => [
                        'name' => $booking->user->name,
                    ],
                    'start_date' => $booking->start_date,
                    'end_date' => $booking->end_date,
                    'status' => $booking->status === 'completed' ? 'ชำระเงินแล้ว' : 'ยังไม่ได้ชำระเงิน',
                ];
            })
        ]);

    }

    /**
     * แสดงฟอร์มจองรถ (แก้ไขให้รับ Car $car และเปลี่ยน Inertia Page)
     */
    public function create(Car $car)
    {
        return inertia('Car/Booking', [
            'car' => $car,
            'user' => Auth::user(), // ✅ ส่ง user ไปด้วย
        ]);
    }

    /**
     * บันทึกข้อมูลการจองรถ
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'car_id' => 'required|exists:cars,id',
            'pickup_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:pickup_date', // ✅ ต้องมี return_date เสมอ
        ]);

        $car = Car::findOrFail($request->car_id);
        $days = Carbon::parse($request->pickup_date)->diffInDays($request->return_date);
        $totalAmount = $days * $car->daily_rate;

        // ✅ เพิ่ม end_date (return_date)
        $booking = Booking::create([
            'user_id' => $request->user_id,
            'car_id' => $request->car_id,
            'start_date' => $request->pickup_date,
            'end_date' => $request->return_date, // ✅ กำหนดวันคืนรถที่ตั้งใจไว้
            'returned_at' => null, // ❌ ยังไม่มีข้อมูล จนกว่าจะคืนรถจริง
            'total_amount' => $totalAmount,
            'status' => 'pending',
        ]);


        return redirect()->route('payments.show', ['booking' => $booking->id]);

    }

    public function history()
{
    $bookings = auth()->user()->bookings()->with('car')->get();

    return Inertia::render('Bookings/History', [
        'bookings' => $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'car' => [
                    'id' => $booking->car->id,  // ดึง car.id
                    'name' => $booking->car->brand . ' ' . $booking->car->name,
                    'image' => $booking->car->image ?? null,
                ],
                'start_date' => $booking->start_date,
                'end_date' => $booking->end_date,
                'status' => $booking->status === 'completed' ? 'ชำระเงินแล้ว' : 'ยังไม่ได้ชำระเงิน'
            ];
        })
    ]);
}




    /**
     * แสดงรายละเอียดการจอง
     */
    public function show($id)
    {
        $booking = Booking::with('car', 'user')->findOrFail($id);
        return inertia('Bookings/Show', ['booking' => $booking]);
    }

    /**
     * ยกเลิกการจอง
     */
    public function cancel($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending bookings can be cancelled.');
        }

        DB::beginTransaction();
        try {
            $booking->update(['status' => 'cancelled']);

            $car = Car::findOrFail($booking->car_id);
            $car->update(['status' => 'available']);

            DB::commit();
            return redirect()->route('bookings.index')->with('success', 'Booking cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to cancel booking: ' . $e->getMessage());
        }
    }

    /**
     * อัปเดตสถานะการจอง (เช่น ยืนยัน หรือ เสร็จสิ้น)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:confirmed,completed,cancelled',
        ]);

        $booking = Booking::findOrFail($id);

        DB::beginTransaction();
        try {
            $booking->update(['status' => $request->status]);

            if ($request->status === 'completed') {
                $car = Car::findOrFail($booking->car_id);
                $car->update(['status' => 'available']);
            }

            DB::commit();
            return redirect()->route('bookings.index')->with('success', 'Booking status updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update booking status: ' . $e->getMessage());
        }
    }
}
