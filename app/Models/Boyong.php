<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boyong extends Model
{
    protected $fillable = [
        'santri_id', 'tahun_mondok', 'tahun_tugas', 'no_surat', 'tanggal_pengajuan', 'tanggal_lulus', 'status_pengajuan', 'keterangan'
    ];

    protected $casts = [
        'status_pengajuan' => \App\Enums\BoyongStatusEnum::class,
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }
}
