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
        Schema::create('surat_keluar', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('nomor_urut');
            $table->date('tanggal_keluar');
            $table->string('tujuan_surat');
            $table->string('perihal');
            $table->foreignId('kode_jenis_id')
                ->constrained('kode_jenis_surat')
                ->cascadeOnDelete();
            $table->foreignId('instansi_id')
                ->constrained('instansi')
                ->cascadeOnDelete();
            $table->text('isi_surat');
            $table->string('file_surat')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keluar');
    }
};
