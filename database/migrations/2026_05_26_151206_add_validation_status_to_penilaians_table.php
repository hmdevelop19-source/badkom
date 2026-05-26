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
        Schema::table('penilaians', function (Blueprint $table) {
            $table->enum('status_badkom_wilayah', ['Menunggu', 'Disetujui', 'Ditolak'])->default('Menunggu');
            $table->enum('status_badkom_pusat', ['Menunggu', 'Disetujui', 'Ditolak'])->default('Menunggu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penilaians', function (Blueprint $table) {
            $table->dropColumn('status_badkom_wilayah');
            $table->dropColumn('status_badkom_pusat');
        });
    }
};
