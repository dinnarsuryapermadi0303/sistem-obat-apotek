<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$terms = [
    'becefrot',
    'brochifar',
    'brochifar plus',
    'berlosid',
    'bronkis',
    'calortusin',
    'cezzvit',
];

foreach ($terms as $term) {
    echo "TERM: {$term}\n";
    $matches = Product::whereRaw('LOWER(nama) LIKE ?', ["%{$term}%"])->orderBy('id')->get();
    foreach ($matches as $m) {
        echo "  ID={$m->id} nama={$m->nama} harga={$m->harga} kategori={$m->kategori}\n";
    }
    echo str_repeat('-', 80) . "\n";
}
