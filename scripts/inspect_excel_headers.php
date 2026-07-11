<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$path = __DIR__ . '/../Daftar obat Keras dan obat Umum.xlsx';
if (!file_exists($path)) {
    echo "File not found: {$path}\n";
    exit(1);
}

$spreadsheet = IOFactory::load($path);
$worksheet = $spreadsheet->getActiveSheet();
$rows = $worksheet->toArray();

for ($i = 0; $i < min(10, count($rows)); $i++) {
    echo "ROW {$i}: ";
    foreach ($rows[$i] as $cell) {
        $cell = trim((string) $cell);
        echo '[' . str_replace("\n", ' ', $cell) . '] ';
    }
    echo "\n";
}

$header = array_map(fn($cell) => strtolower(trim((string) $cell)), $rows[2] ?? []);
echo "\nHEADER CLEAN: " . json_encode($header) . "\n";
