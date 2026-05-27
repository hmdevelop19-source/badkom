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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // e.g., 'string', 'integer', 'boolean'
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default setting
        DB::table('settings')->insert([
            'key' => 'target_tugas_wajib',
            'value' => '3',
            'type' => 'integer',
            'description' => 'Target jumlah penilaian "Lulus" yang sah bagi Santri untuk menyelesaikan tugas wajib',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
