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
        Schema::create('jawaban_laporans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_wajib_id')->constrained()->onDelete('cascade');
            $table->foreignId('soal_laporan_id')->constrained()->onDelete('cascade');
            $table->text('jawaban')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_laporans');
    }
};
