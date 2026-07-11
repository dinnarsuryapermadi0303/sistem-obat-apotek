<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

// find suspicious products where nama looks like a description or has unexpected length
$list = Product::where('nama', 'LIKE', '%merupakan%')
    ->orWhere('nama', 'LIKE', '%mengandung%')
    ->orWhereRaw('LENGTH(nama) > 80')
    ->orderBy('id')
    ->get();

echo "Found {$list->count()} suspicious products:\n";
foreach ($list as $p) {
    echo "ID: {$p->id}\n";
    echo "Nama: {$p->nama}\n";
    echo "Kategori: {$p->kategori}\n";
    echo "Jenis: {$p->jenis}\n";
    echo "Indikasi: {$p->indikasi}\n";
    echo "Deskripsi: {$p->deskripsi}\n";
    echo "Dosis: {$p->dosis}\n";
    echo "Cara_pakai: {$p->cara_pakai}\n";
    echo "Efek_samping: {$p->efek_samping}\n";
    echo "Harga: {$p->harga}\n";
    echo str_repeat('-', 50) . "\n";
}
