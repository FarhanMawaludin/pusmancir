<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Menambahkan enum baru 'selesai'
        DB::statement("ALTER TABLE peminjaman_paket MODIFY status ENUM('menunggu', 'berhasil', 'tolak', 'selesai') DEFAULT 'menunggu'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback ke enum sebelumnya tanpa 'selesai'
        DB::statement("ALTER TABLE peminjaman_paket MODIFY status ENUM('menunggu', 'berhasil', 'tolak') DEFAULT 'menunggu'");
    }
};

