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
        Schema::create('surat_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat'); // nomor asli dari luar
            $table->date('tanggal_terima');
            $table->string('asal_surat');
            $table->string('perihal');
            $table->unsignedInteger('lampiran')->nullable();
            $table->string('file_surat')->nullable(); // path file PDF
            $table->enum('status', ['Diterima', 'Didisposisikan', 'Selesai'])->default('Diterima');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_masuk');
    }
};
