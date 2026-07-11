<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Helpers\ExcelHelper;

$path = __DIR__ . '/../Daftar obat Keras dan obat Umum.xlsx';
if (!file_exists($path)) {
    echo "Excel file not found: {$path}\n";
    exit(1);
}

$spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load($path);
$worksheet = $spreadsheet->getActiveSheet();
$rows = $worksheet->toArray();

for ($i = 0; $i < min(20, count($rows)); $i++) {
    echo "ROW {$i}: ";
    $row = $rows[$i];
    foreach ($row as $cell) {
        $cell = trim((string) $cell);
        if ($cell !== '') {
            echo '[' . str_replace("\n", ' ', substr($cell, 0, 60)) . '] ';
        }
    }
    echo "\n";
}

echo "\nParsed data sample:\n";
$data = ExcelHelper::readObatData($path);
foreach (array_slice($data, 0, 20) as $idx => $item) {
    echo "#{$idx}: nama=" . ($item['nama'] ?? '') . " | kategori=" . ($item['kategori'] ?? '') . " | harga=" . ($item['harga'] ?? '') . "\n";
}
