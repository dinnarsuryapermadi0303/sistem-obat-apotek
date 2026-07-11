<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$badIds = [247, 249, 250, 251, 252, 256, 257];
$good = Product::whereRaw('LOWER(nama) NOT LIKE ?', ['%mengandung%'])
    ->whereRaw('CHAR_LENGTH(nama) <= ?', [120])
    ->get();
$goodNames = $good->pluck('nama')->map(fn($name) => trim(strtolower($name)))->unique()->values()->all();

foreach ($badIds as $id) {
    $product = Product::find($id);
    if (!$product) {
        echo "Missing ID {$id}\n";
        continue;
    }
    $badName = strtolower(trim($product->nama));
    $matches = [];
    foreach ($goodNames as $goodName) {
        if ($goodName === '') continue;
        if (str_starts_with($badName, $goodName)) {
            $matches[] = $goodName;
        }
    }
    echo "ID {$id}: {$product->nama}\n";
    echo "len=" . strlen($product->nama) . "\n";
    echo "starts_with matches: " . json_encode($matches) . "\n";
    echo str_repeat('-', 80) . "\n";
}
