<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanMendesak extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tahun_ajaran_id',
        'judul',
        'isi_laporan',
        'file_lampiran',
        'status_penyelesaian',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
