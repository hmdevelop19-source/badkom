<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoalLaporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'target_level',
        'pertanyaan',
        'tipe_soal',
        'opsi_jawaban',
        'is_active',
    ];

    protected $casts = [
        'opsi_jawaban' => 'array',
        'is_active' => 'boolean',
    ];
}
