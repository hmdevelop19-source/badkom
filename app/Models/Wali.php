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

    public function provinsiModel()
    {
        return $this->belongsTo(Provinsi::class, 'id_prov');
    }

    public function kabupatenModel()
    {
        return $this->belongsTo(Kabupaten::class, 'id_kab');
    }

    public function kecamatanModel()
    {
        return $this->belongsTo(Kecamatan::class, 'id_kec');
    }

    public function kelurahanModel()
    {
        return $this->belongsTo(Kelurahan::class, 'id_kel');
    }

    protected $appends = ['provinsi', 'kabupaten', 'kecamatan', 'desa'];

    public function getProvinsiAttribute()
    {
        return $this->provinsiModel ? $this->provinsiModel->nama : null;
    }

    public function getKabupatenAttribute()
    {
        return $this->kabupatenModel ? $this->kabupatenModel->nama : null;
    }

    public function getKecamatanAttribute()
    {
        return $this->kecamatanModel ? $this->kecamatanModel->nama : null;
    }

    public function getDesaAttribute()
    {
        return $this->kelurahanModel ? $this->kelurahanModel->nama : null;
    }
}
