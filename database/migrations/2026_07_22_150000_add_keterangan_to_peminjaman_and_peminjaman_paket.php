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
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->text('keterangan')->nullable()->after('status');
        });

        Schema::table('peminjaman_paket', function (Blueprint $table) {
            $table->text('keterangan')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });

        Schema::table('peminjaman_paket', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};
