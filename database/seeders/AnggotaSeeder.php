<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Anggota;

class AnggotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { {
            // Pastikan ada user & kelas dulu (jika belum disediakan oleh seeder lain)
            $user = User::first() ?? User::factory()->create();
            $kelas = Kelas::first(); // boleh nullable

            Anggota::create([
                'no_telp' => '081234567890',
                'email' => 'anggota@example.com',
                'email_verified_at' => now(),
                'user_id' => $user->id,
                'kelas_id' => $kelas?->id, // menggunakan null-safe operator (PHP 8+)
            ]);

            // Tambahan contoh random
            foreach (range(1, 5) as $i) {
                $user = User::factory()->create();

                Anggota::create([
                    'no_telp' => '08' . rand(1000000000, 9999999999),
                    'email' => 'anggota' . $i . '@example.com',
                    'email_verified_at' => now(),
                    'user_id' => $user->id,
                    'kelas_id' => $kelas?->id,
                ]);
            }
        }
    }
}
