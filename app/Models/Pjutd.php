<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pjutd extends Model
{
    protected $fillable = [
        'kode_lembaga', 'nama_pjutd', 'yayasan', 'badkom_id',
        'nama_madrasah', 'no_hp', 'alamat',
        'id_prov', 'id_kab', 'id_kec', 'id_kel'
    ];

    public function badkom()
    {
        return $this->belongsTo(Badkom::class);
    }

    public function utds()
    {
        return $this->hasMany(Utd::class);
    }

    public function penilaianPjutds()
    {
        return $this->hasMany(PenilaianPjutd::class);
    }
}
