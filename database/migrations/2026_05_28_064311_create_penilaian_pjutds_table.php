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
        Schema::create('penilaian_pjutds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pjutd_id')->constrained('pjutds')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->onDelete('cascade');
            $table->enum('predikat', ['A', 'B', 'C', 'D']);
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Ensure a PJUTD is only evaluated once per academic year
            $table->unique(['pjutd_id', 'tahun_ajaran_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_pjutds');
    }
};
