<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanUtd extends Model
{
    protected $fillable = ['nis', 'judul', 'isi', 'file', 'status'];
}
