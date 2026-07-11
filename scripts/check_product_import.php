<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$count = Product::count();
echo "count={$count}\n";
foreach (Product::orderBy('nama')->limit(5)->get() as $p) {
    echo $p->nama . ' | ' . ($p->kategori ?? '-') . ' | ' . ($p->harga ?? '-') . "\n";
}
