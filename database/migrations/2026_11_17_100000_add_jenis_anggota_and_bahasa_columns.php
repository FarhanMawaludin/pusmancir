<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * - anggota.jenis_anggota   : 'siswa' | 'guru'
     * - anggota.jenis_identitas : 'NISN' | 'NIP' | 'NIK'
     *   (nomor identitas tetap disimpan di kolom `nisn` agar seluruh alur
     *    peminjaman/scan kartu yang sudah ada tetap berjalan untuk guru)
     * - inventori.bahasa        : 'Indonesia' | 'Inggris' | 'Lainnya' (untuk Buku Induk)
     */
    public function up(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            if (!Schema::hasColumn('anggota', 'jenis_anggota')) {
                $table->string('jenis_anggota', 20)->default('siswa');
            }
            if (!Schema::hasColumn('anggota', 'jenis_identitas')) {
                $table->string('jenis_identitas', 10)->default('NISN');
            }
        });

        Schema::table('inventori', function (Blueprint $table) {
            if (!Schema::hasColumn('inventori', 'bahasa')) {
                $table->string('bahasa', 20)->nullable()->default('Indonesia');
            }
        });
    }

    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->dropColumn(['jenis_anggota', 'jenis_identitas']);
        });

        Schema::table('inventori', function (Blueprint $table) {
            $table->dropColumn('bahasa');
        });
    }
};
