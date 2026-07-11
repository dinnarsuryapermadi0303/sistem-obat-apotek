<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Helpers\ExcelHelper;
use App\Models\Product;

$path = ExcelHelper::resolveExcelPath();
$data = ExcelHelper::readObatData($path);
$counts = [];
foreach ($data as $item) {
    $cat = $item['kategori'] ?? 'Unknown';
    if (!isset($counts[$cat])) {
        $counts[$cat] = 0;
    }
    $counts[$cat]++;
}
echo "Parsed Excel counts:\n";
foreach ($counts as $cat => $count) {
    echo "  $cat: $count\n";
}
echo "  Total: " . count($data) . "\n\n";

db:
$products = Product::selectRaw('kategori, count(*) as total')->groupBy('kategori')->get();
echo "DB counts:\n";
foreach ($products as $row) {
    echo "  {$row->kategori}: {$row->total}\n";
}
echo "  Total: " . Product::count() . "\n";
