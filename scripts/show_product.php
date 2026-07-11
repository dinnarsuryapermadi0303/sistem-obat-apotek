<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$products = Product::where('nama', 'LIKE', '%Akita%')->orWhere('nama', 'Akita')->get();
if ($products->isEmpty()) {
    echo "No product found for Akita\n";
    exit(0);
}
foreach ($products as $product) {
    echo "ID: {$product->id}\n";
    echo "Nama: {$product->nama}\n";
    echo "Kategori: {$product->kategori}\n";
    echo "Jenis: {$product->jenis}\n";
    echo "Indikasi: {$product->indikasi}\n";
    echo "Deskripsi: {$product->deskripsi}\n";
    echo "Dosis: {$product->dosis}\n";
    echo "Cara_pakai: {$product->cara_pakai}\n";
    echo "Efek_samping: {$product->efek_samping}\n";
    echo "Kontraindikasi: {$product->kontraindikasi}\n";
    echo "Harga: {$product->harga}\n";
    echo str_repeat('-', 40) . "\n";
}
