<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boyong extends Model
{
    protected $fillable = [
        'santri_id', 'no_surat', 'tanggal_pengajuan', 'tanggal_lulus', 'status_pengajuan', 'keterangan'
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }
}
