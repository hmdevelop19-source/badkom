<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    protected $table = 'kabupaten';
    protected $fillable = ['provinsi_id', 'kode', 'nama'];

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class);
    }

    public function kecamatans()
    {
        return $this->hasMany(Kecamatan::class);
    }
}
