<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penarikans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utd_id')->constrained('utds')->onDelete('cascade');
            $table->foreignId('pjutd_id')->constrained('pjutds')->onDelete('cascade');
            $table->text('alasan')->nullable();
            $table->date('tanggal_penarikan');
            $table->foreignId('diproses_oleh')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penarikans');
    }
};
