<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$product = Product::find(247);
if (!$product) {
    echo "Product 247 not found\n";
    exit(0);
}

echo "ID=247 nama='{$product->nama}' len=" . strlen($product->nama) . "\n";
$good = Product::whereRaw('LOWER(nama) = ?', ['becefrot'])->first();
if ($good) {
    echo "Good ID={$good->id} nama='{$good->nama}' len=" . strlen($good->nama) . "\n";
    $badName = strtolower(trim($product->nama));
    $goodName = strtolower(trim($good->nama));
    echo "starts_with? " . (str_starts_with($badName, $goodName) ? 'yes' : 'no') . "\n";
    echo "contains? " . (str_contains($badName, $goodName) ? 'yes' : 'no') . "\n";
}
