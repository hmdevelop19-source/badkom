<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanWajib extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tahun_ajaran_id',
        'bulan_tahun',
        'kategori_bulan',
        'status',
        'status_waktu',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function jawabans()
    {
        return $this->hasMany(JawabanLaporan::class);
    }
}
