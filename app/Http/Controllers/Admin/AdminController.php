<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\User;
use App\Models\Booking;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    /**
     * แสดงแดชบอร์ดของแอดมิน
     */
    public function dashboard()
    {
        $totalCars = Car::count();
        $totalUsers = User::count();
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();

        return inertia('Admin/Dashboard', [
            'totalCars' => $totalCars,
            'totalUsers' => $totalUsers,
            'totalBookings' => $totalBookings,
            'pendingBookings' => $pendingBookings
        ]);
    }

    /**
     * จัดการรายการรถทั้งหมด
     */
    public function manageCars()
    {
        $cars = Car::latest()->get();
        return inertia('Admin/Cars', ['cars' => $cars]);
    }

    /**
     * จัดการรายการผู้ใช้ทั้งหมด
     */
    public function manageUsers()
    {
        $users = User::latest()->get();
        return inertia('Admin/Users', ['users' => $users]);
    }

    /**
     * จัดการรายการจองทั้งหมด
     */
    public function manageBookings()
    {
        $bookings = Booking::with('car', 'user')->latest()->get();
        return inertia('Admin/Bookings', ['bookings' => $bookings]);
    }

    /**
     * อัปเดตสถานะการจอง
     */
    public function updateBookingStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:confirmed,completed,cancelled',
        ]);

        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending bookings can be updated.');
        }

        $booking->update(['status' => $request->status]);

        return redirect()->route('admin.bookings')->with('success', 'Booking status updated.');
    }

    /**
     * ลบรถ
     */
    public function deleteCar($id)
    {
        $car = Car::findOrFail($id);
        if ($car->status === 'rented') {
            return redirect()->back()->with('error', 'Cannot delete a rented car.');
        }
        $car->delete();
        return redirect()->route('admin.cars')->with('success', 'Car deleted successfully.');
    }

    /**
     * ลบผู้ใช้
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->bookings()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete a user with active bookings.');
        }
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }
}
