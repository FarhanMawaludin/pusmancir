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
        Schema::table('anggota', function (Blueprint $table) {
            $table->string('no_telp')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->foreignId('kelas_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->string('no_telp')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->foreignId('kelas_id')->nullable(false)->change();
        });
    }
};
