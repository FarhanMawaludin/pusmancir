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
        Schema::create('katalog', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_inventori')
                ->constrained('inventori')
                ->onDelete('cascade');
            $table->string('kategori_buku')->nullable();    
            $table->string('judul_buku')->nullable();
            $table->string('pengarang')->nullable();
            $table->string('penerbit')->nullable();
            $table->string('isbn')->nullable();
            $table->string('cover_buku')->nullable();       
            $table->text('ringkasan_buku')->nullable();
            $table->string('kode_ddc')->nullable();
            $table->string('no_panggil')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('katalog');
    }
};
