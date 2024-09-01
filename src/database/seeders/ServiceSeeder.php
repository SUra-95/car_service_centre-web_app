<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
            ],
            [
                'name' => 'Body wash',
                'section' => 'Washing',
                'price' => '3000',
            ],
            [
                'name' => 'Vaccum',
                'section' => 'Interior Cleaning',
                'price' => '2000',
            ],
            [
                'name' => 'Shampoo',
                'section' => 'Interior Cleaning',
                'price' => '1500',
            ],
            [
                'name' => 'Engine oil replacement',
                'section' => 'Service',
                'price' => '1000',
            ],
            [
                'name' => 'Brake oil replacement',
                'section' => 'Service',
                'price' => '1000',
            ],
            [
                'name' => 'Coolnat replacement',
                'section' => 'Service',
                'price' => '1200',
            ],
            [
                'name' => 'Air Filter replacement',
                'section' => 'Service',
                'price' => '2500',
            ],
            [
                'name' => 'Oil Filter replacement',
                'section' => 'Service',
                'price' => '2000',
            ],
            [
                'name' => 'AC Filter replacement',
                'section' => 'Service',
                'price' => '3000',
            ],
            [
                'name' => 'Brake Shoe replacement',
                'section' => 'Service',
                'price' => '1800',
            ],
            
        ]);
    }
}
