<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utd extends Model
{
    protected $fillable = ['santri_id', 'pjutd_id'];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function pjutd()
    {
        return $this->belongsTo(Pjutd::class);
    }
}
