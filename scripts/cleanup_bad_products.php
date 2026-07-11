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

$good = Product::where(function ($q) {
    $q->whereRaw('LOWER(nama) NOT LIKE ?', ['%mengandung%'])
        ->whereRaw('LENGTH(nama) <= 120');
})->get();

$goodNames = $good->pluck('nama')->map(fn($n) => strtolower($n))->unique()->toArray();

foreach ($bad as $product) {
    $nameLower = strtolower($product->nama);
    $matched = [];
    foreach ($goodNames as $goodName) {
        if ($goodName !== '' && str_contains($nameLower, $goodName)) {
            $matched[] = $goodName;
        }
    }

    echo "ID {$product->id}\n";
    echo "bad nama: {$product->nama}\n";
    echo "good matches: " . json_encode($matched) . "\n";
    echo str_repeat('-', 80) . "\n";
}
