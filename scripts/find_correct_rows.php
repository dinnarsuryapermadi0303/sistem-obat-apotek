<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$names = [
    'Alleron',
    'Alpara',
    'Akita',
    'Antasida DOEN',
    'Asam Folat 1 mg',
    'Bufantacid',
    'Becefrot',
    'Beneuron',
    'Brochifar',
    'Brochifar Plus',
    'Berlosid',
    'Bronkis',
    'Becom-Zet',
    'Caviplek',
    'Chlorpheniramine Maleate (CTM)',
    'Calortusin',
    'Cezzvit',
    'Calcium Lactate',
];

foreach ($names as $name) {
    $product = Product::whereRaw('LOWER(TRIM(nama)) = ?', [strtolower(trim($name))])->first();
    echo "Search: {$name}\n";
    if ($product) {
        echo "FOUND id={$product->id} nama={$product->nama} harga={$product->harga}\n";
    } else {
        echo "NOT FOUND\n";
    }
    echo str_repeat('-', 40) . "\n";
}
