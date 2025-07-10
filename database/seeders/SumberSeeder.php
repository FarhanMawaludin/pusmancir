<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sumber')->insert([
            ['nama' => 'BOS'],
            ['nama' => 'Perpusnas'],
            ['nama' => 'Pusnas'],
        ]);
    }
}
