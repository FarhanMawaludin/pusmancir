<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            KelasSeeder::class,
            SekolahSeeder::class,
            JenisMediaSeeder::class,
            JenisSumberSeeder::class,
            SumberSeeder::class,
            KategoriBukuSeeder::class,
            PenerbitSeeder::class,
            AnggotaSeeder::class,
            BeritaSeeder::class,
        ]);
    }
}
