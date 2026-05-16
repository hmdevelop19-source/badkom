<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badkom extends Model
{
    protected $fillable = ['kode_badkom', 'nama_badkom'];

    public function pjutds()
    {
        return $this->hasMany(Pjutd::class);
    }
}
