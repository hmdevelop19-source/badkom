<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPermohonan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pjutd_id',
        'badkom_id',
        'tahun_ajaran_id',
        'jenis_permohonan',
        'pemohon_nama',
        'pemohon_umur',
        'pemohon_jabatan',
        'pemohon_alamat',
        'kriteria_ustadz',
        'fasilitas_tempat_tinggal',
        'fasilitas_kamar_mandi',
        'fasilitas_wc',
        'fasilitas_bisyaroh',
        'fasilitas_konsumsi',
        'pjutd_nama_lembaga',
        'pjutd_alamat',
        'pjutd_nama_kepala',
        'pjutd_kurikulum',
        'bakat_kemampuan_1',
        'bakat_kemampuan_2',
        'bakat_kemampuan_3',
        'status',
        'tahun_ajaran_tujuan'
    ];

    protected $casts = [
        'fasilitas_tempat_tinggal' => 'boolean',
        'fasilitas_kamar_mandi' => 'boolean',
        'fasilitas_wc' => 'boolean',
        'fasilitas_bisyaroh' => 'boolean',
        'fasilitas_konsumsi' => 'boolean',
    ];

    public function pjutd()
    {
        return $this->belongsTo(Pjutd::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function badkom()
    {
        return $this->belongsTo(Badkom::class);
    }
}
