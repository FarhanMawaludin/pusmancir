<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisSumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jenis_sumber')->insert([
            ['nama_sumber' => 'Pembelian'],
            ['nama_sumber' => 'Hibah'],
            ['nama_sumber' => 'Sumbangan'],
        ]);
    }
}
