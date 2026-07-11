<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cats = ['Obat Keras' => 0, 'Obat Umum' => 0];
foreach (App\Models\Product::all() as $p) {
    $c = $p->kategori ?? 'Unknown';
    if (isset($cats[$c])) {
        $cats[$c]++;
    }
}

foreach ($cats as $k => $v) {
    echo $k . ': ' . $v . PHP_EOL;
}
