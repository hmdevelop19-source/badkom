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
        Schema::table('laporan_wajibs', function (Blueprint $table) {
            $table->foreignId('tahun_ajaran_id')->nullable()->after('user_id')->constrained('tahun_ajarans')->onDelete('cascade');
            $table->dropUnique(['user_id', 'kategori_bulan']);
            $table->unique(['user_id', 'tahun_ajaran_id', 'kategori_bulan'], 'lap_wajib_user_ta_kategori_unique');
        });

        Schema::table('laporan_mendesaks', function (Blueprint $table) {
            $table->foreignId('tahun_ajaran_id')->nullable()->after('user_id')->constrained('tahun_ajarans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_wajibs', function (Blueprint $table) {
            $table->dropUnique('lap_wajib_user_ta_kategori_unique');
            $table->dropForeign(['tahun_ajaran_id']);
            $table->dropColumn('tahun_ajaran_id');
            $table->unique(['user_id', 'kategori_bulan']);
        });

        Schema::table('laporan_mendesaks', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->dropColumn('tahun_ajaran_id');
        });
    }
};
