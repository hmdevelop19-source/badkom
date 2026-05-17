<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    protected $table = 'santris';
    protected $fillable = [
        'nis', 'nama', 'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 
        'alamat', 'nama_ortu', 'nama_wali_kelas', 'no_hp', 'email',
        'id_prov', 'id_kab', 'id_kec', 'id_kel'
    ];

    public function utd()
    {
        return $this->hasOne(Utd::class);
    }
}
