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
        Schema::table('penarikans', function (Blueprint $table) {
            $table->string('status_penyelesaian')->default('Tidak Tuntas')->after('alasan');
        });

        Schema::table('mutasis', function (Blueprint $table) {
            $table->string('status_penyelesaian')->default('Tidak Tuntas')->after('alasan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penarikans', function (Blueprint $table) {
            $table->dropColumn('status_penyelesaian');
        });

        Schema::table('mutasis', function (Blueprint $table) {
            $table->dropColumn('status_penyelesaian');
        });
    }
};
