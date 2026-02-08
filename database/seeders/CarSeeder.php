<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Car;

class CarSeeder extends Seeder
{
    public function run()
    {
        $cars = [ // ✅ อาร์เรย์ของข้อมูลรถ ที่จะถูกเพิ่มลงในฐานข้อมูล
            [
                'brand' => 'Toyota',
                'model' => 'Corolla',
                'plate_number' => 'XYZ-1234',
                'year' => 2022,
                'color' => 'White',
                'seats' => 4,
                'daily_rate' => 1200.00,
                'description' => 'Sedan ประหยัดน้ำมัน',
                'image' => 'toyota-corolla.jpg',
                'status' => 'available',
            ],
            [
                'brand' => 'Honda',
                'model' => 'Civic',
                'plate_number' => 'ABC-5678',
                'year' => 2023,
                'color' => 'Black',
                'seats' => 4,
                'daily_rate' => 1400.00,
                'description' => 'Sedan สปอร์ต',
                'image' => 'honda-civic.jpg',
                'status' => 'available',
            ],
            [
                'brand' => 'Nissan',
                'model' => 'Almera',
                'plate_number' => 'LMN-9012',
                'year' => 2021,
                'color' => 'Silver',
                'seats' => 4,
                'daily_rate' => 1000.00,
                'description' => 'รถเล็ก ประหยัดน้ำมัน',
                'image' => 'nissan-almera.jpg',
                'status' => 'rented',
            ],
        ];

        foreach ($cars as $car) { // ✅ วนลูปเพื่อเพิ่มข้อมูลรถลงในฐานข้อมูล
            Car::updateOrCreate(
                ['brand' => $car['brand'], 'model' => $car['model']], // ✅ ใช้คู่ brand + model
                $car
            );
        }
    }
}
