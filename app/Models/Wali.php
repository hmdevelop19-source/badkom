<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wali extends Model
{
    protected $fillable = [
        'nik', 'nama_wali', 'no_hp', 'email', 'id_prov', 'id_kab', 'id_kec', 'id_kel', 'alamat'
    ];

    public function santris()
    {
        return $this->hasMany(Santri::class);
    }
}
