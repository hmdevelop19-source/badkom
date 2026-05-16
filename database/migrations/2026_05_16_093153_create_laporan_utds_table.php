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
        Schema::create('laporan_utds', function (Blueprint $table) {
            $table->id();
            $table->string('nis'); // Can be santri nis or pjutd kode_lembaga
            $table->string('judul');
            $table->text('isi')->nullable();
            $table->string('file')->nullable();
            $table->integer('status')->default(0); // 0: Santri, 1: Pjutd
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_utds');
    }
};
