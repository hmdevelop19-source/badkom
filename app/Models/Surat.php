<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    protected $fillable = ['id_pengirim', 'id_penerima', 'subjek', 'isi', 'lampiran'];
}
