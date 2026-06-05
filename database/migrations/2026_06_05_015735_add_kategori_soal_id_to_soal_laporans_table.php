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
        Schema::table('soal_laporans', function (Blueprint $table) {
            $table->foreignId('kategori_soal_id')->nullable()->constrained('kategori_soals')->onDelete('set null');
            $table->integer('urutan')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soal_laporans', function (Blueprint $table) {
            $table->dropForeign(['kategori_soal_id']);
            $table->dropColumn(['kategori_soal_id', 'urutan']);
        });
    }
};
