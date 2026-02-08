<?php
// ใช้สร้างข้อมูลรถยนต์แบบสุ่มเพื่อใช้ทดสอบ โดยสุ่ม ยี่ห้อ, รุ่น, ปี, สี, ราคา, สถานะ และตั้งค่ารูปภาพให้สัมพันธ์กับ Car ID

namespace Database\Factories;

use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    protected $model = Car::class;

    //  กำหนดข้อมูลจำลองสำหรับ Car
    public function definition()
    {   //เลือก ยี่ห้อรถ จากรายการที่กำหนดไว้
        $brand = $this->faker->randomElement(['Toyota', 'Honda', 'Nissan', 'BMW', 'Mercedes', 'Audi']);
        $models = [
            'Toyota' => ['Camry', 'Corolla', 'Yaris', 'Fortuner'],
            'Honda' => ['Civic', 'City', 'Accord', 'CR-V'],
            'Nissan' => ['Almera', 'March', 'Kicks', 'X-Trail'],
            'BMW' => ['320i', '520d', 'X3', 'X5'],
            'Mercedes' => ['C200', 'E300', 'GLC', 'GLE'],
            'Audi' => ['A4', 'A6', 'Q5', 'Q7']
        ];

        $model = $this->faker->randomElement($models[$brand]);

        return [
            'brand' => $brand,
            'model' => $model,
            'plate_number' => strtoupper($this->faker->lexify('??')) . ' ' . $this->faker->numberBetween(1000, 9999),
            'year' => $this->faker->numberBetween(2018, 2024),
            'color' => $this->faker->randomElement(['Red', 'Blue', 'Black', 'White', 'Gray']),
            'seats' => $this->faker->randomElement([4, 5, 7]),
            'daily_rate' => $this->faker->randomFloat(2, 800, 3000),
            'description' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['available', 'rented']),
            'image' => 'cars/default.jpg',
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Car $car) {
            // แก้ไขค่า image ให้ใช้ ID ของรถจริง
            $car->image = "cars/" . strtolower(str_replace(' ', '_', $car->brand . '_' . $car->model)) . "_{$car->id}.jpg";
            $car->save();
        });
    }
}
