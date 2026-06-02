<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penarikan extends Model
{
    protected $fillable = [
        'utd_id', 'pjutd_id', 'alasan', 'tanggal_penarikan', 'status_penyelesaian', 'diproses_oleh'
    ];

    protected $casts = [
        'tanggal_penarikan' => 'date',
    ];

    public function utd()
    {
        return $this->belongsTo(Utd::class);
    }

    public function pjutd()
    {
        return $this->belongsTo(Pjutd::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
