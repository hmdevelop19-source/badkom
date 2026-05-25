<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanMendesak extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'judul',
        'isi_laporan',
        'file_lampiran',
        'status_penyelesaian',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
