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
        Schema::table('santris', function (Blueprint $table) {
            $table->dropColumn(['nama_ortu', 'nama_wali_kelas', 'no_hp', 'email']);
            $table->foreignId('wali_id')->nullable()->constrained('walis')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('santris', function (Blueprint $table) {
            $table->dropForeign(['wali_id']);
            $table->dropColumn('wali_id');
            $table->string('nama_ortu')->nullable();
            $table->string('nama_wali_kelas')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
        });
    }
};
