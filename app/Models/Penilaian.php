<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    protected $fillable = ['utd_id', 'keterangan', 'predikat', 'catatan', 'status_badkom_wilayah', 'status_badkom_pusat'];

    public function utd()
    {
        return $this->belongsTo(Utd::class);
    }
}
