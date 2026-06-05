<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriSoal extends Model
{
    use HasFactory;

    protected $fillable = ['nama_kategori', 'target_level', 'urutan'];

    public function soalLaporan()
    {
        return $this->hasMany(SoalLaporan::class)->orderBy('urutan', 'asc');
    }
}
