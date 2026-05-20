<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = 'kecamatan';
    protected $fillable = ['kabupaten_id', 'kode', 'nama'];

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }

    public function kelurahans()
    {
        return $this->hasMany(Kelurahan::class);
    }
}
