<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalLaporanWajib extends Model
{
    protected $fillable = [
        'tahun_ajaran_id',
        'kategori_bulan',
        'batas_tanggal',
    ];

    protected $casts = [
        'batas_tanggal' => 'date',
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
