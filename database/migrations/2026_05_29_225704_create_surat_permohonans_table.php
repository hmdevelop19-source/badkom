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
        Schema::create('surat_permohonans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pjutd_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained()->cascadeOnDelete();
            
            $table->enum('jenis_permohonan', ['Baru', 'Perpanjangan'])->default('Baru');
            
            // Data Pemohon
            $table->string('pemohon_nama');
            $table->string('pemohon_umur')->nullable();
            $table->string('pemohon_jabatan')->nullable();
            $table->string('pemohon_alamat')->nullable();
            
            // Kriteria Ustadz (Diniyah dan Umumiyah, Diniyah, Umumiyah)
            $table->enum('kriteria_ustadz', ['diniyah_umumiyah', 'diniyah', 'umumiyah'])->default('diniyah');
            
            // Fasilitas (Ada / Tidak Ada) -> stored as boolean true/false
            $table->boolean('fasilitas_tempat_tinggal')->default(false);
            $table->boolean('fasilitas_kamar_mandi')->default(false);
            $table->boolean('fasilitas_wc')->default(false);
            $table->boolean('fasilitas_bisyaroh')->default(false);
            $table->boolean('fasilitas_konsumsi')->default(false);
            
            // Tipe Utama Bakat dan Kemampuan (1, 2, 3)
            $table->string('bakat_kemampuan_1')->nullable();
            $table->string('bakat_kemampuan_2')->nullable();
            $table->string('bakat_kemampuan_3')->nullable();
            
            // Untuk tracking (opsional)
            $table->enum('status', ['Menunggu', 'Disetujui', 'Ditolak'])->default('Menunggu');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_permohonans');
    }
};
