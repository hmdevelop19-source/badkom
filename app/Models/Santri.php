<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    protected $table = 'santris';
    protected $fillable = [
        'nis', 'nama', 'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 
        'alamat', 'id_prov', 'id_kab', 'id_kec', 'id_kel', 'wali_id', 'status_santri', 'keahlian'
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

    public function user()
    {
        return $this->hasOne(User::class, 'santri_id');
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
