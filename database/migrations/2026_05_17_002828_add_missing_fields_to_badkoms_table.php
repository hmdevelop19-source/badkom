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
        Schema::table('badkoms', function (Blueprint $table) {
            $table->string('nama_pj')->nullable();
            $table->string('email')->nullable();
            $table->string('wilayah_koordinasi')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('badkoms', function (Blueprint $table) {
            $table->dropColumn(['nama_pj', 'email', 'wilayah_koordinasi', 'alamat', 'no_hp']);
        });
    }
};
