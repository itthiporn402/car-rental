<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        // ปิด Foreign Key Checks ชั่วคราว
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // ล้างข้อมูล
        DB::table('users')->truncate();

        // เปิด Foreign Key Checks กลับมา
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // สร้าง Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // สร้าง User ทั่วไป 10 คน
        User::factory(10)->create();
    }
}
