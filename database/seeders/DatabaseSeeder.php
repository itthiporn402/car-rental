<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            CarSeeder::class,
            BookingSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
