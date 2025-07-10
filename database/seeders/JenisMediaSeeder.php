<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class JenisMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jenis_media')->insert([
            ['nama_jenis_media' => 'Buku'],
            ['nama_jenis_media' => 'Majalah'],
            ['nama_jenis_media' => 'CD-ROM'],
        ]);
        
    }
}
