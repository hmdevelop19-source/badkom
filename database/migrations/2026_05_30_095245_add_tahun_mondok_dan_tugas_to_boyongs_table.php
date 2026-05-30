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
        Schema::table('boyongs', function (Blueprint $table) {
            $table->string('tahun_mondok')->nullable()->after('santri_id');
            $table->string('tahun_tugas')->nullable()->after('tahun_mondok');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boyongs', function (Blueprint $table) {
            //
        });
    }
};
