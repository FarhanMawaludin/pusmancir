<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('antrian_paket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota')->onDelete('cascade');
            $table->date('tanggal_kunjungan');
            $table->integer('nomor_antrian');
            $table->string('status')->default('menunggu'); // menunggu, hadir, batal, tidak_hadir
            $table->timestamps();

            $table->unique(['tanggal_kunjungan', 'nomor_antrian']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('antrian_paket');
    }
};
