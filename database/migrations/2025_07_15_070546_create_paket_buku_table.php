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
        Schema::create('paket_buku', function (Blueprint $table) {
            $table->id();
            $table->string('nama_paket');
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('stok_total')->default(0);
            $table->unsignedInteger('stok_tersedia')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket_buku');
    }
};
