<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $fillable = ['nama_tahun_ajaran', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
