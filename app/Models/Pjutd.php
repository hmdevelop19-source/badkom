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
        return $this->hasMany(Utd::class, 'pjutd_id');
    }

    public function scopeForUserRole($query, $user)
    {
        if ($user && $user->level === 'badkom_wilayah') {
            return $query->where('badkom_id', $user->badkom_id);
        } elseif ($user && $user->level === 'pjutd') {
            return $query->where('id', $user->pjutd_id);
        }
        return $query;
    }

    public function penilaianPjutds()
    {
        return $this->hasMany(PenilaianPjutd::class);
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
