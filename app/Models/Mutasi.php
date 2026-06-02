<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mutasi extends Model
{
    protected $fillable = [
        'utd_id',
        'asal_pjutd_id',
        'tujuan_pjutd_id',
        'alasan',
        'tanggal_mutasi',
        'status_penyelesaian',
        'diproses_oleh'
    ];

    public function utd()
    {
        return $this->belongsTo(Utd::class);
    }

    public function asalPjutd()
    {
        return $this->belongsTo(Pjutd::class, 'asal_pjutd_id');
    }

    public function tujuanPjutd()
    {
        return $this->belongsTo(Pjutd::class, 'tujuan_pjutd_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
