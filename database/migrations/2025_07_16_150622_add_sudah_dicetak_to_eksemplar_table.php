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
        Schema::table('eksemplar', function (Blueprint $table) {
            $table->boolean('sudah_dicetak')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eksemplar', function (Blueprint $table) {
            $table->dropColumn('sudah_dicetak');
        });
    }
};
