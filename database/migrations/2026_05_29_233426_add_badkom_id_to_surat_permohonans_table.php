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
            $table->foreignId('badkom_id')->nullable()->constrained('badkoms')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_permohonans', function (Blueprint $table) {
            $table->dropForeign(['badkom_id']);
            $table->dropColumn('badkom_id');
        });
    }
};
