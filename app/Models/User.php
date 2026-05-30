<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    protected $fillable = [
        'username',
        'fullname',
        'email',
        'password',
        'level',
        'badkom_id',
        'pjutd_id',
        'santri_id',
        'foto_profil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function badkom()
    {
        return $this->belongsTo(Badkom::class);
    }

    public function pjutd()
    {
        return $this->belongsTo(Pjutd::class);
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function laporanWajibs()
    {
        return $this->hasMany(LaporanWajib::class);
    }

    public function laporanMendesaks()
    {
        return $this->hasMany(LaporanMendesak::class);
    }
}
