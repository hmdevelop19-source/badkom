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
            $table->enum('status_waktu', ['Tepat Waktu', 'Tidak Tepat Waktu'])->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_wajibs', function (Blueprint $table) {
            $table->dropColumn('status_waktu');
        });
    }
};
