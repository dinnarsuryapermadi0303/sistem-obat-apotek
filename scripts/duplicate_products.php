<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$products = Product::orderBy('nama')->get();
$groups = [];
foreach ($products as $product) {
    $key = preg_replace('/[^a-z0-9]+/', '', strtolower($product->nama));
    if ($key === '') {
        continue;
    }
    $groups[$key][] = $product;
}

foreach ($groups as $key => $items) {
    if (count($items) > 1) {
        echo "GROUP {$key}\n";
        foreach ($items as $item) {
            echo "  ID={$item->id} nama={$item->nama} harga={$item->harga} kategori={$item->kategori} deskripsi=" . substr($item->deskripsi, 0, 80) . "\n";
        }
        echo str_repeat('-', 80) . "\n";
    }
}
