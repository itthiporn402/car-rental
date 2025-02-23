<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Car;
use Illuminate\Support\Facades\DB;

/**
 * แสดงรายการการจองทั้งหมด
 */
class AdminBookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('car', 'user')->latest()->get();
        return inertia('Admin/Bookings', ['bookings' => $bookings]);
    }

    /**
     * แสดงรายละเอียดของการจอง
     */
    public function show($id)
    {
        $booking = Booking::with(['car', 'user'])->findOrFail($id);
        return inertia('Admin/Bookings/Show', ['booking' => $booking]);
    }

    /**
     * อัปเดตสถานะการจอง
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:confirmed,completed,cancelled',
        ]);

        $booking = Booking::findOrFail($id);

        if ($booking->status === 'completed' || $booking->status === 'cancelled') {
            return redirect()->back()->with('error', 'Cannot update a completed or cancelled booking.');
        }

        $booking->update(['status' => $request->status]);

        return redirect()->route('admin.bookings.index')->with('success', 'Booking status updated successfully.');
    }

    /**
     * อนุมัติการจอง
     */
    public function approve($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending bookings can be approved.');
        }

        $booking->update(['status' => 'approved']);

        return redirect()->route('admin.bookings.index')->with('success', 'Booking approved successfully.');
    }

    /**
     * ปฏิเสธการจอง
     */
    public function reject($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending bookings can be rejected.');
        }

        DB::beginTransaction();
        try {
            $booking->update(['status' => 'rejected']);

            $car = Car::findOrFail($booking->car_id);
            $car->update(['status' => 'available']);

            DB::commit();
            return redirect()->route('admin.bookings.index')->with('success', 'Booking rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to reject booking: ' . $e->getMessage());
        }
    }

    /**
     * ลบการจอง
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status === 'confirmed') {
            return redirect()->back()->with('error', 'Cannot delete a confirmed booking.');
        }

        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted successfully.');
    }
}
