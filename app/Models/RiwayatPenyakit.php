<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPenyakit extends Model
{
    protected $fillable = [
        'pasien_id',
        'keluhan',
        'durasi',
        'riwayat_penyakit',
        'status',
    ];

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }
}
