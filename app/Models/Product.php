<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama',
        'kategori',
        'jenis',
        'deskripsi',
        'indikasi',
        'komposisi',
        'dosis',
        'efek_samping',
        'kontraindikasi',
        'harga',
    ];

    protected $casts = [
        'indikasi' => 'string',
        'komposisi' => 'string',
        'dosis' => 'string',
        'efek_samping' => 'string',
        'kontraindikasi' => 'string',
    ];
}
