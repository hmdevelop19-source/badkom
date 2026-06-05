<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoalLaporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'target_level',
        'kategori_soal_id',
        'pertanyaan',
        'tipe_soal',
        'opsi_jawaban',
        'is_active',
        'urutan',
    ];

    public function kategoriSoal()
    {
        return $this->belongsTo(KategoriSoal::class);
    }

    protected $casts = [
        'opsi_jawaban' => 'array',
        'is_active' => 'boolean',
    ];
}
