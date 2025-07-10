<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kelas')->insert([
            [
                'nama_kelas' => '10 IPA 1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_kelas' => '10 IPA 2',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_kelas' => '10 IPA 3',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
