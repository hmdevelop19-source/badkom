<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utd extends Model
{
    protected $fillable = ['santri_id', 'pjutd_id', 'tahun_ajaran_id'];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function pjutd()
    {
        return $this->belongsTo(Pjutd::class, 'pjutd_id');
    }

    public function penilaian()
    {
        return $this->hasOne(Penilaian::class);
    }
}
