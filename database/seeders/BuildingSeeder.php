<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Building;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $buildings = [
            [
                'name' => 'Gedung A',
                'is_active' => true,
                'create_by' => 1, // Sesuaikan dengan user ID yang valid
                'update_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gedung B',
                'is_active' => true,
                'create_by' => 1,
                'update_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gedung C',
                'is_active' => false,
                'create_by' => 1,
                'update_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gedung D',
                'is_active' => true,
                'create_by' => 1,
                'update_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Building::insert($buildings);
    }
}
