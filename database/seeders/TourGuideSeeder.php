<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TourGuideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cek apakah table 'tour_guides' tersedia sebelum menjalankan seeder
        if (Schema::hasTable('tour_guides')) {
            DB::table('tour_guides')->insert([
                [
                    'name' => 'John Doe',
                    'is_active' => true,
                    'create_by' => 1, // Sesuaikan ID user
                    'update_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Jane Smith',
                    'is_active' => true,
                    'create_by' => 2, // Sesuaikan ID user
                    'update_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Samuel Green',
                    'is_active' => false,
                    'create_by' => 3, // Sesuaikan ID user
                    'update_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
