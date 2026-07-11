<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Helpers\ExcelHelper;

$path = __DIR__ . '/../storage/app/Daftar obat Keras dan obat Umum.xlsx';
$data = ExcelHelper::readObatData($path);
$empty = array_filter($data, fn($it) => trim(($it['nama'] ?? '')) === '');
echo "Total parsed: " . count($data) . "\n";
echo "Empty nama count: " . count($empty) . "\n";
foreach (array_slice(array_values($empty), 0, 40) as $i => $it) {
    echo sprintf("%03d: kategori=%s | kategori_raw=%s | harga=%s | deskripsi=%s\n", $i + 1, $it['kategori'] ?? '', $it['kategori_raw'] ?? '', $it['harga'] ?? '', substr($it['deskripsi'] ?? '', 0, 80));
}
