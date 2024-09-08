<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('services')->insert([
            [
                'name' => 'Full wash',
                'section' => 'Washing',
                'price' => '5000',
                'time_duration_minutes' => 60,
            ],
            [
                'name' => 'Body wash',
                'section' => 'Washing',
                'price' => '3000',
                'time_duration_minutes' => 45,
            ],
            [
                'name' => 'Vaccum',
                'section' => 'Interior Cleaning',
                'price' => '2000',
                'time_duration_minutes' => 30,
            ],
            [
                'name' => 'Shampoo',
                'section' => 'Interior Cleaning',
                'price' => '1500',
                'time_duration_minutes' => 40,
            ],
            [
                'name' => 'Engine oil replacement',
                'section' => 'Service',
                'price' => '1000',
                'time_duration_minutes' => 20,
            ],
            [
                'name' => 'Brake oil replacement',
                'section' => 'Service',
                'price' => '1000',
                'time_duration_minutes' => 30,
            ],
            [
                'name' => 'Coolant replacement',
                'section' => 'Service',
                'price' => '1200',
                'time_duration_minutes' => 25,
            ],
            [
                'name' => 'Air Filter replacement',
                'section' => 'Service',
                'price' => '2500',
                'time_duration_minutes' => 15,
            ],
            [
                'name' => 'Oil Filter replacement',
                'section' => 'Service',
                'price' => '2000',
                'time_duration_minutes' => 15,
            ],
            [
                'name' => 'AC Filter replacement',
                'section' => 'Service',
                'price' => '3000',
                'time_duration_minutes' => 30,
            ],
            [
                'name' => 'Brake Shoe replacement',
                'section' => 'Service',
                'price' => '1800',
                'time_duration_minutes' => 50,
            ],
        ]);
    }
}
