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
     * ดึงรายการจองทั้งหมดจากฐานข้อมูล พร้อมข้อมูลของ รถยนต์ (car) และ ผู้จอง (user)
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
     * เมื่อผู้ใช้คลิก จองรถ ระบบจะแสดงฟอร์ม เลือกวันรับ - คืนรถ และแสดงข้อมูลรถ
     */
    public function create(Car $car)
    {
        return inertia('Car/Booking', [
            'car' => $car, // รับข้อมูล car ที่ต้องการจอง
            'user' => Auth::user(), // ✅ ส่งข้อมูลผู้ใช้ที่ล็อกอินไปด้วย
        ]);
    }

    /**
     * บันทึกข้อมูลการจองลงฐานข้อมูล และคำนวณ ค่าเช่ารถ ตามจำนวนวันที่เลือก
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'car_id' => 'required|exists:cars,id',
            'pickup_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:pickup_date', // ✅ return_date ห้ามน้อยกว่า pickup_date
        ]);

        $car = Car::findOrFail($request->car_id);
        $days = Carbon::parse($request->pickup_date)->diffInDays($request->return_date);
        $totalAmount = $days * $car->daily_rate;  //คำนวณราคาทั้งหมด (days * daily_rate)

        // ตรวจสอบว่ามีการจองซ้ำในช่วงเวลาเดียวกันหรือไม่
        $existingBooking = Booking::where('car_id', $request->car_id)
            ->where(function ($query) use ($request) {
                // ตรวจสอบว่ามีการจองที่ใช้ start_date และ end_date ซ้ำกัน
                $query->where('start_date', '=', $request->pickup_date)
                    ->where('end_date', '=', $request->return_date);
            })
            ->first();

        if ($existingBooking) {
            // ถ้ามีการจองซ้ำ ให้แสดงข้อความผิดพลาดและยังคงอยู่หน้าเดิม
            return redirect()->back()->withErrors(['message' => 'คุณเคยจองรถคันนี้ในช่วงเวลานี้แล้ว']);
        }

        $booking = Booking::create([ // สร้างการจอง และบันทึกข้อมูลลงฐานข้อมูล
            'user_id' => $request->user_id,
            'car_id' => $request->car_id,
            'start_date' => $request->pickup_date,
            'end_date' => $request->return_date,
            'returned_at' => null,
            'total_amount' => $totalAmount,
            'status' => 'pending',
        ]);


        return redirect()->route('payments.show', ['booking' => $booking->id]);

    }

    /**
     * ดึงประวัติการจองของผู้ใช้ที่ล็อกอินอยู่
     */

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
     * ยกเลิกการจอง (pending เท่านั้น) ระบบจะเปลี่ยนสถานะเป็น cancelled
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
            $car->update(['status' => 'available']);    // คืนสถานะรถเป็น available

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

    /**
     * ลบข้อมูลการจอง
     */
    public function destroy($id)
    {
        $booking = Booking::find($id);

        if ($booking) {
            $booking->delete();
            // ตอบกลับเป็น JSON พร้อมข้อความยืนยัน
            return response()->json(['message' => 'Success!!!']);
        }

        return response()->json(['message' => 'Error!']);
    }


}
