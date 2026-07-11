<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Helpers\ExcelHelper;

$excelPath = ExcelHelper::resolveExcelPath();
$rows = ExcelHelper::readObatData($excelPath);
if (!file_exists($excelPath)) {
    echo "ERROR: Excel file not found at {$excelPath}\n";
    exit(1);
}
if (!is_array($rows)) {
    echo "ERROR: Failed to read Excel rows.\n";
    exit(1);
}

$backupDir = __DIR__ . '/../storage/app/backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}
$backupFile = $backupDir . '/products_backup_' . date('Ymd_His') . '.json';
file_put_contents($backupFile, Product::withTrashed()->get()->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Remove old product data permanently before importing the latest Excel data.
Product::withTrashed()->forceDelete();

echo "Cleared existing products permanently before import.\n";

$processed = 0;
$created = 0;
$updated = 0;
$skipped = 0;

foreach ($rows as $row) {
    $name = trim($row['nama'] ?? '');
    if ($name === '') {
        $skipped++;
        continue;
    }

    $data = [
        'nama' => $name,
        'kategori' => $row['kategori'] ?? ($row['kategori_raw'] ?? 'Obat Umum'),
        'jenis' => $row['jenis'] ?? null,
        'deskripsi' => $row['deskripsi'] ?? null,
        'indikasi' => $row['indikasi'] ?? null,
        'komposisi' => $row['komposisi'] ?? null,
        'dosis' => $row['dosis'] ?? null,
        'efek_samping' => $row['efek_samping'] ?? null,
        'kontraindikasi' => $row['kontraindikasi'] ?? null,
        'harga' => App\Helpers\ExcelHelper::parsePrice($row['harga'] ?? ''),
    ];

    $lowerName = strtolower($name);
    $existing = Product::whereRaw('LOWER(TRIM(nama)) = ?', [$lowerName])->first();
    if (!$existing && !empty($row['deskripsi'])) {
        $existing = Product::whereRaw('LOWER(TRIM(deskripsi)) = ?', [strtolower(trim($row['deskripsi']))])->first();
    }
    if (!$existing && !empty($row['indikasi'])) {
        $existing = Product::whereRaw('LOWER(TRIM(indikasi)) = ?', [strtolower(trim($row['indikasi']))])->first();
    }
    if (!$existing) {
        $existing = Product::whereRaw('LOWER(nama) LIKE ?', ["%{$lowerName}%"])->first();
    }

    if ($existing) {
        $existing->update($data);
        $updated++;
    } else {
        Product::create($data);
        $created++;
    }

    $processed++;
}

echo "Import finished. Processed={$processed}, Created={$created}, Updated={$updated}, Skipped={$skipped}.\n";
echo "Backup written to: {$backupFile}\n";
