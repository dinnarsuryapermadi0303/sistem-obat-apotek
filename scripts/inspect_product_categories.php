<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$counts = [];
foreach (App\Models\Product::all() as $p) {
    $c = (string) ($p->kategori ?? '');
    $c = trim($c);
    if ($c === '') $c = '[empty]';
    $counts[$c] = ($counts[$c] ?? 0) + 1;
}
ksort($counts);
foreach ($counts as $k => $v) {
    echo $k . ': ' . $v . PHP_EOL;
}
