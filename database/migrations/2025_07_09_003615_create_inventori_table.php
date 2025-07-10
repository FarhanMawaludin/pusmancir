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
        Schema::create('inventori', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel jenis_media
            $table->foreignId('id_jenis_media')
                  ->constrained('jenis_media')
                  ->onDelete('cascade');

            $table->integer('jumlah_eksemplar');
            $table->date('tanggal_pembelian');

            // Relasi ke jenis_sumber, sumber, kategori_buku
            $table->foreignId('id_jenis_sumber')
                  ->constrained('jenis_sumber')
                  ->onDelete('cascade');

            $table->foreignId('id_sumber')
                  ->constrained('sumber')
                  ->onDelete('cascade');

            $table->foreignId('id_kategori_buku')
                  ->constrained('kategori_buku')
                  ->onDelete('cascade');

            // Detail buku
            $table->string('judul_buku');
            $table->string('pengarang');

            // Relasi ke sekolah dan penerbit
            $table->foreignId('id_sekolah')
                  ->constrained('sekolah')
                  ->onDelete('cascade');

            $table->foreignId('id_penerbit')
                  ->constrained('penerbit')
                  ->onDelete('cascade');

            // Harga
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('total_harga', 15, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventori');
    }
};
