<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    protected $table = 'santris';
    protected $fillable = [
        'nis', 'nama', 'id_prov', 'id_kab', 'id_kec', 'id_kel'
    ];

    public function utd()
    {
        return $this->hasOne(Utd::class);
    }
}
