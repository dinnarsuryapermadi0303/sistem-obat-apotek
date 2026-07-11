<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$bad = Product::where(function ($q) {
    $q->whereRaw('LOWER(nama) LIKE ?', ['%mengandung%'])
        ->orWhereRaw('LENGTH(nama) > 120');
})->orderBy('id')->get();

$allNames = Product::pluck('nama')->map(fn($n) => strtolower($n))->toArray();

foreach ($bad as $product) {
    echo "ID {$product->id}\n";
    echo "nama: {$product->nama}\n";
    echo "deskripsi: {$product->deskripsi}\n";
    echo "indikasi: {$product->indikasi}\n";
    echo "harga: {$product->harga}\n";
    echo "---- best match by substring: \n";
    $nameLower = strtolower($product->nama);
    foreach ($allNames as $candidate) {
        if ($candidate === strtolower($product->nama)) continue;
        if ($candidate !== '' && str_contains($nameLower, $candidate)) {
            echo "  MATCH candidate={$candidate}\n";
        }
    }
    echo str_repeat('=', 80) . "\n";
}
