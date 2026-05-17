<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badkom extends Model
{
    protected $fillable = [
        'kode_badkom', 'nama_badkom', 'nama_pj', 'email', 'wilayah_koordinasi', 'alamat', 'no_hp'
    ];

    public function pjutds()
    {
        return $this->hasMany(Pjutd::class);
    }
}
