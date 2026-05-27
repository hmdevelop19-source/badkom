<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    protected $table = 'santris';
    protected $fillable = [
        'nis', 'nama', 'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 
        'alamat', 'id_prov', 'id_kab', 'id_kec', 'id_kel', 'wali_id', 'status_santri'
    ];

    public function wali()
    {
        return $this->belongsTo(Wali::class);
    }

    public function utds()
    {
        return $this->hasMany(Utd::class, 'santri_id');
    }

    public function boyong()
    {
        return $this->hasOne(Boyong::class, 'santri_id');
    }
}
