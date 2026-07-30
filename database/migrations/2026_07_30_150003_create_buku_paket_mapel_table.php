<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku_paket_mapel', function (Blueprint $table) {
            $table->id();
            $table->string('nama_buku');
            $table->string('tingkat_kelas'); // 'X', 'XI IPA', 'XI IPS', 'XII IPA', 'XII IPS'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku_paket_mapel');
    }
};
