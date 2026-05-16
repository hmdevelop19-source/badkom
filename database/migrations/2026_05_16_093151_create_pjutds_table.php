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
        Schema::create('pjutds', function (Blueprint $table) {
            $table->id();
            $table->string('kode_lembaga')->unique();
            $table->string('nama_pjutd');
            $table->string('yayasan')->nullable();
            $table->foreignId('badkom_id')->constrained('badkoms')->onDelete('cascade');
            $table->unsignedBigInteger('id_prov')->nullable();
            $table->unsignedBigInteger('id_kab')->nullable();
            $table->unsignedBigInteger('id_kec')->nullable();
            $table->unsignedBigInteger('id_kel')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pjutds');
    }
};
