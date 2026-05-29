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
        Schema::table('surat_permohonans', function (Blueprint $table) {
            $table->string('pjutd_nama_lembaga')->nullable();
            $table->string('pjutd_alamat')->nullable();
            $table->string('pjutd_nama_kepala')->nullable();
            $table->string('pjutd_kurikulum')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_permohonans', function (Blueprint $table) {
            $table->dropColumn([
                'pjutd_nama_lembaga',
                'pjutd_alamat',
                'pjutd_nama_kepala',
                'pjutd_kurikulum'
            ]);
        });
    }
};
