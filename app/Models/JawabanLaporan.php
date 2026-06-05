<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JawabanLaporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'laporan_wajib_id',
        'soal_laporan_id',
        'jawaban',
    ];

    public function laporanWajib()
    {
        return $this->belongsTo(LaporanWajib::class);
    }

    public function soalLaporan()
    {
        return $this->belongsTo(SoalLaporan::class);
    }
}
