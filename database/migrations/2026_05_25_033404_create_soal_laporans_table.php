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
        Schema::create('soal_laporans', function (Blueprint $table) {
            $table->id();
            $table->enum('target_level', ['utd', 'pjutd', 'badkom_wilayah'])->comment('Siapa yang wajib mengisi soal ini');
            $table->text('pertanyaan');
            $table->enum('tipe_soal', ['uraian', 'pilihan_ganda']);
            $table->json('opsi_jawaban')->nullable()->comment('Menyimpan array string jika tipe_soal adalah pilihan_ganda');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soal_laporans');
    }
};
