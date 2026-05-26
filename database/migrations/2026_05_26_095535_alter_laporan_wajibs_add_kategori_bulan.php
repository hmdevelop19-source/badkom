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
            $table->dropUnique(['user_id', 'bulan_tahun']);
            $table->string('kategori_bulan')->nullable()->after('bulan_tahun');
            $table->unique(['user_id', 'kategori_bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_wajibs', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'kategori_bulan']);
            $table->dropColumn('kategori_bulan');
            $table->unique(['user_id', 'bulan_tahun']);
        });
    }
};
