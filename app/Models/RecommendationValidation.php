<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class RecommendationValidation extends Model
{
    protected $fillable = [

        'kode',

        'nama',

        'usia',

        'keluhan',

        'durasi',

        'riwayat',

        'obat',

        'approved_meds',
        'recommended_meds',

        'similarity',

        'confidence',

        'status',

        'user_status',

        'admin_status',

        'admin_conditions',

        'catatan_admin',

        'pdf_ready',

        'pdf_path',

        'approved_by',

        'approved_at'

    ];

    protected $casts = [

        'approved_at' => 'datetime',

        'pdf_ready' => 'boolean',

        'approved_meds' => 'array',
        'recommended_meds' => 'array',

    ];

    public function getDisplayTimestampAttribute(): Carbon
    {
        $timestamp = $this->approved_at
            ?? $this->created_at
            ?? $this->updated_at
            ?? Carbon::now('Asia/Jakarta');

        $carbon = $timestamp instanceof Carbon
            ? $timestamp
            : Carbon::parse($timestamp);

        return $carbon->setTimezone('Asia/Jakarta');
    }

    public function getDisplayTimestampFormattedAttribute(): string
    {
        return $this->display_timestamp->format('d-m-Y H:i:s');
    }
}
