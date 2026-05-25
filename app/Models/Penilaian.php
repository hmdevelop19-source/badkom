<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    protected $fillable = ['utd_id', 'keterangan', 'predikat', 'catatan'];

    public function utd()
    {
        return $this->belongsTo(Utd::class);
    }
}
