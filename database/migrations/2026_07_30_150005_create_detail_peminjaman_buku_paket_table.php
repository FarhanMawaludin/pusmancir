<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_peminjaman_buku_paket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_buku_paket_id')->constrained('peminjaman_buku_paket')->onDelete('cascade');
            $table->foreignId('buku_paket_mapel_id')->constrained('buku_paket_mapel')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_peminjaman_buku_paket');
    }
};
