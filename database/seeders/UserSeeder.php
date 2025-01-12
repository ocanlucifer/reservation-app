<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin', // Role default
            'is_active' => true, // Flag aktif
        ]);

        User::create([
            'name' => 'Test User Humas',
            'username' => 'humas',
            'email' => 'humas@example.com',
            'password' => Hash::make('humas123'),
            'role' => 'humas',
            'is_active' => false, // Flag aktif
        ]);

        User::create([
            'name' => 'Building Management.',
            'username' => 'building',
            'email' => 'building@example.com',
            'password' => Hash::make('building123'),
            'role' => 'building',
            'is_active' => false, // Flag aktif
        ]);

        User::create([
            'name' => 'Test User Koordinator',
            'username' => 'koordinator',
            'email' => 'koordinator@example.com',
            'password' => Hash::make('koordinator123'),
            'role' => 'koordinator',
            'is_active' => false, // Flag aktif
        ]);

        User::create([
            'name' => 'Test User Visitor',
            'username' => 'visitor',
            'email' => 'visitor@example.com',
            'password' => Hash::make('visitor123'),
            'role' => 'visitor',
            'is_active' => false, // Flag aktif
        ]);
    }
}
