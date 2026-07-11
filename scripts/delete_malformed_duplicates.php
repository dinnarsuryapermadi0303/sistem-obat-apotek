<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$backupDir = __DIR__ . '/../storage/app/backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}
$backupFile = $backupDir . '/products_backup_before_cleanup_' . date('Ymd_His') . '.json';
file_put_contents($backupFile, Product::all()->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Backup saved to: {$backupFile}\n";

$bad = Product::where(function ($q) {
    $q->whereRaw('LOWER(nama) LIKE ?', ['%mengandung%'])
        ->orWhereRaw('LOWER(nama) LIKE ?', ['%merupakan%'])
        ->orWhereRaw('CHAR_LENGTH(nama) > ?', [80]);
})->orderBy('id')->get();

$good = Product::where(function ($q) {
    $q->whereRaw('LOWER(nama) NOT LIKE ?', ['%mengandung%'])
        ->whereRaw('LOWER(nama) NOT LIKE ?', ['%merupakan%'])
        ->whereRaw('CHAR_LENGTH(nama) <= ?', [80]);
})->get();

$goodNames = $good->pluck('nama')->map(fn($name) => trim(strtolower($name)))->unique()->values()->all();

$deleted = 0;

foreach ($bad as $product) {
    $badName = trim(strtolower($product->nama));
    $matches = [];
    foreach ($goodNames as $goodName) {
        if ($goodName === '') {
            continue;
        }
        if (str_starts_with($badName, $goodName) && strlen($goodName) < strlen($badName)) {
            $matches[] = $goodName;
        }
    }

    if (empty($matches)) {
        echo "Skipping ID {$product->id}: no shorter good prefix match found for '{$product->nama}'\n";
        continue;
    }

    usort($matches, fn($a, $b) => strlen($b) <=> strlen($a));
    $best = $matches[0];
    $bestMatches = array_filter($matches, fn($m) => strlen($m) === strlen($best));

    if (count($bestMatches) === 1) {
        echo "Deleting malformed duplicate ID {$product->id} matching '{$best}'\n";
        $product->delete();
        $deleted++;
    } else {
        echo "Skipping ID {$product->id}: ambiguous prefix matches " . implode(', ', $bestMatches) . "\n";
    }
}

echo "Deleted {$deleted} malformed rows.\n";
