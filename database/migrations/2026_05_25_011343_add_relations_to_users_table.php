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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('badkom_id')->nullable()->constrained('badkoms')->nullOnDelete();
            $table->foreignId('pjutd_id')->nullable()->constrained('pjutds')->nullOnDelete();
            $table->foreignId('santri_id')->nullable()->constrained('santris')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['badkom_id']);
            $table->dropForeign(['pjutd_id']);
            $table->dropForeign(['santri_id']);
            $table->dropColumn(['badkom_id', 'pjutd_id', 'santri_id']);
        });
    }
};
