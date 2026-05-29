<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianPjutd extends Model
{
    protected $fillable = [
        'pjutd_id',
        'tahun_ajaran_id',
        'predikat',
        'catatan'
    ];

    public function pjutd()
    {
        return $this->belongsTo(Pjutd::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
