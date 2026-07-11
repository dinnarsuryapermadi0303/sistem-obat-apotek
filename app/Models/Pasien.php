<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasien extends Model
{
    protected $fillable = [
        'nama',
        'usia',
        'email',
        'nomor_telepon',
        'alamat',
        'jenis_kelamin',
        'tanggal_lahir',
        'tanggal_daftar',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_daftar' => 'datetime',
    ];

    public function riwayatPenyakits(): HasMany
    {
        return $this->hasMany(RiwayatPenyakit::class);
    }

    public function rekomendasis(): HasMany
    {
        return $this->hasMany(Rekomendasi::class);
    }
}
