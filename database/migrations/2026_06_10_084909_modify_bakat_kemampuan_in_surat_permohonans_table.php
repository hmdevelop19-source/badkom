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
            $table->dropColumn(['bakat_kemampuan_1', 'bakat_kemampuan_2', 'bakat_kemampuan_3']);
            $table->json('bakat_kemampuan')->nullable()->after('fasilitas_konsumsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_permohonans', function (Blueprint $table) {
            $table->dropColumn('bakat_kemampuan');
            $table->string('bakat_kemampuan_1')->nullable();
            $table->string('bakat_kemampuan_2')->nullable();
            $table->string('bakat_kemampuan_3')->nullable();
        });
    }
};
