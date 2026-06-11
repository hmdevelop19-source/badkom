<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badkom extends Model
{
    protected $fillable = [
        'kode_badkom', 'nama_pj', 'email', 'wilayah_koordinasi', 'alamat', 'no_hp'
    ];

    public function pjutds()
    {
        return $this->hasMany(Pjutd::class);
    }

    public function scopeForUserRole($query, $user)
    {
        if ($user && $user->level === 'badkom_wilayah') {
            return $query->where('id', $user->badkom_id);
        }
        return $query;
    }
}
