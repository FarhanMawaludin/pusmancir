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
        Schema::create('eksemplar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_inventori')->constrained('inventori')->onDelete('cascade');
            $table->string('no_induk')->unique();
            $table->string('no_inventori')->unique();
            $table->string('no_rfid')->nullable();
            $table->enum('status', ['tersedia', 'dipinjam', 'rusak', 'hilang'])->default('tersedia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eksemplar');
    }
};
