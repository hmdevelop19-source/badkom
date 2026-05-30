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
        Schema::create('mutasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utd_id')->constrained('utds')->onDelete('cascade');
            $table->foreignId('asal_pjutd_id')->constrained('pjutds')->onDelete('cascade');
            $table->foreignId('tujuan_pjutd_id')->constrained('pjutds')->onDelete('cascade');
            $table->text('alasan');
            $table->date('tanggal_mutasi');
            $table->foreignId('diproses_oleh')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasis');
    }
};
