<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')
                ->constrained('peminjaman')
                ->cascadeOnDelete();

            $table->foreignId('eksemplar_id')
                ->constrained('eksemplar')
                ->cascadeOnDelete();

            $table->date('tanggal_kembali_asli')->nullable(); // waktu dikembalikan
            $table->foreignId('user_id')->nullable()
                ->constrained('users')
                ->nullOnDelete(); // petugas yang menerima
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_peminjaman');
    }
};
