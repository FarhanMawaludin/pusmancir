<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'username' => 'admin',
                'password' => Hash::make('admin'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Wawan Sutawan',
                'username' => '0052234567',
                'password' => Hash::make('0052234567'),
                'role' => 'anggota',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Pustakawan',
                'username' => 'pustakawan',
                'password' => Hash::make('pustakawan'),
                'role' => 'pustakawan',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
