<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$bad = Product::where(function ($q) {
    $q->whereRaw('LOWER(nama) LIKE ?', ['%mengandung%'])
        ->orWhereRaw('CHAR_LENGTH(nama) > ?', [120]);
})->orderBy('id')->get();

$good = Product::where(function ($q) {
    $q->whereRaw('LOWER(nama) NOT LIKE ?', ['%mengandung%'])
        ->whereRaw('CHAR_LENGTH(nama) <= ?', [120]);
})->get();

$goodNames = $good->pluck('nama')->map(fn($name) => trim(strtolower($name)))->unique()->values()->all();

foreach ($bad as $product) {
    echo "ID {$product->id}\n";
    echo "nama: {$product->nama}\n";
    echo "good match candidates:\n";
    foreach ($goodNames as $goodName) {
        if ($goodName !== '' && str_contains(strtolower($product->nama), $goodName)) {
            echo "  candidate: {$goodName}\n";
        }
    }
    echo str_repeat('-', 80) . "\n";
}
