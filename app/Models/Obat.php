<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Obat extends Model
{
    protected $fillable = [
        'nama',
        'kategori',
        'deskripsi',
        'indikasi',
        'durasi',
        'harga',
        'stok',
        'gambar',
    ];

    public function rekomendasis(): HasMany
    {
        return $this->hasMany(Rekomendasi::class);
    }
}
