<?php

use App\Models\Barangay;
use Illuminate\Database\Seeder;

class BarangayTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Barangay::insert([
            [
                'name' => 'Bagong Ilog',
            ],
            [
                'name' => 'Bagong Katipunan',
            ],
            [
                'name' => 'Bambang',
            ],
            [
                'name' => 'Buting',
            ],
            [
                'name' => 'Caniogan',
            ],
            [
                'name' => 'Dela Paz',
            ],
            [
                'name' => 'Kalawaan',
            ],
            [
                'name' => 'Kapasigan',
            ],
            [
                'name' => 'Kapitolyo',
            ],
            [
                'name' => 'Malinao',
            ],
            [
                'name' => 'Manggahan',
            ],
            [
                'name' => 'Maybunga',
            ],
            [
                'name' => 'Oranbo',
            ],
            [
                'name' => 'Palatiw',
            ],
            [
                'name' => 'Pinagbuhatan',
            ],
            [
                'name' => 'Pineda',
            ],
            [
                'name' => 'Rosario',
            ],
            [
                'name' => 'Sagad',
            ],
            [
                'name' => 'San Antonio',
            ],
            [
                'name' => 'San Joaquin',
            ],
            [
                'name' => 'San Jose',
            ],
            [
                'name' => 'San Miguel',
            ],
            [
                'name' => 'San Nicolas',
            ],
            [
                'name' => 'Santa Cruz',
            ],
            [
                'name' => 'Santa Lucia',
            ],
            [
                'name' => 'Santa Rosa',
            ],
            [
                'name' => 'Santolan',
            ],
            [
                'name' => 'Santa Tomas',
            ],
            [
                'name' => 'Sumilang',
            ],
            [
                'name' => 'Ugong',
            ],
        ]);
    }
}
