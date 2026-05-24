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
        Schema::create('walis', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique()->nullable();
            $table->string('nama_wali');
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('id_prov')->nullable();
            $table->unsignedBigInteger('id_kab')->nullable();
            $table->unsignedBigInteger('id_kec')->nullable();
            $table->unsignedBigInteger('id_kel')->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('walis');
    }
};
