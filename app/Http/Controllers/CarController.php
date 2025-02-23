<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CarController extends Controller
{
    /**
     * แสดงรายการรถยนต์ทั้งหมด
     */
    public function index()
    {
        $cars = Car::all()->map(function ($car) {
            return [
                'id' => $car->id,
                'brand' => $car->brand,
                'model' => $car->model,
                'image' => $car->image, // ใช้ URL จาก database ตรง ๆ
                'status' => $car->status,
                'daily_rate' => $car->daily_rate,
                'seats' => $car->seats,
            ];
        });


        // ตรวจสอบค่าของ image โดยละเอียด


        return Inertia::render('Car/Cars', ['cars' => $cars]);
    }



    /**
     * แสดงฟอร์มสร้างรถใหม่
     */
    public function create()
    {
        return inertia('Car/Create'); // ✅ เปลี่ยนเป็น 'Car/Create' (ถ้ามีไฟล์นี้)
    }

    /**
     * บันทึกข้อมูลรถใหม่
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'year' => 'required|integer|min:2000|max:' . date('Y'),
            'price_per_day' => 'required|numeric|min:0',
            'status' => 'required|in:available,rented,maintenance',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // ตรวจสอบไฟล์
        ]);

        DB::beginTransaction();
        try {
            $imagePath = $request->file('image')->store('cars', 'public');

            Car::create([
                'name' => $request->name,
                'brand' => $request->brand,
                'year' => $request->year,
                'price_per_day' => $request->price_per_day,
                'status' => $request->status,
                'image' => $imagePath,  // ✅ ใช้ path ที่ถูกต้อง
            ]);

            DB::commit();
            return redirect()->route('cars.index')->with('success', 'Car added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to add car: ' . $e->getMessage());
        }
    }


    /**
     * แสดงรายละเอียดรถ
     */
    public function show($id)
    {
        $car = Car::findOrFail($id);
        return inertia('Car/Booking', ['car' => $car]); // ✅ เปลี่ยนเป็น 'Car/Booking'
    }

    /**
     * แสดงฟอร์มแก้ไขรถ
     */
    public function edit($id)
    {
        $car = Car::findOrFail($id);
        return inertia('Car/Edit', ['car' => $car]); // ✅ เปลี่ยนเป็น 'Car/Edit' (ถ้ามีไฟล์นี้)
    }

    /**
     * อัปเดตข้อมูลรถ
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'year' => 'required|integer|min:2000|max:' . date('Y'),
            'price_per_day' => 'required|numeric|min:0',
            'status' => 'required|in:available,rented,maintenance',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $car = Car::findOrFail($id);

        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                // ✅ ลบรูปเก่า
                if ($car->image) {
                    Storage::disk('public')->delete($car->image);
                }
                // ✅ อัปโหลดรูปใหม่
                $car->image = $request->file('image')->store('cars', 'public');
            }

            $car->update([
                'name' => $request->name,
                'brand' => $request->brand,
                'year' => $request->year,
                'price_per_day' => $request->price_per_day,
                'status' => $request->status,
            ]);

            DB::commit();
            return redirect()->route('cars.index')->with('success', 'Car updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update car: ' . $e->getMessage());
        }
    }


    /**
     * ลบรถ
     */
    public function destroy($id)
    {
        $car = Car::findOrFail($id);

        if ($car->image) {
            Storage::disk('public')->delete($car->image);
        }

        $car->delete();

        return redirect()->route('cars.index')->with('success', 'Car deleted successfully.');
    }

}
