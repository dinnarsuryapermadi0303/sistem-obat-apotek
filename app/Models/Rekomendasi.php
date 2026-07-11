<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rekomendasi extends Model
{
    protected $fillable = [
        'pasien_id',
        'obat_id',
        'alasan',
        'skor',
        'status',
        'tanggal_rekomendasi',
    ];

    protected $casts = [
        'tanggal_rekomendasi' => 'datetime',
    ];

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }
}
