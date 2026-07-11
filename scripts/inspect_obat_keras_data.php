<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$path = __DIR__ . '/../storage/app/Daftar obat Keras dan obat Umum.xlsx';
if (!file_exists($path)) {
    echo "Excel file not found: {$path}\n";
    exit(1);
}
$spreadsheet = IOFactory::load($path);
$sheet = $spreadsheet->getSheetByName('Obat Keras');
if (!$sheet) {
    echo "Sheet Obat Keras not found\n";
    exit(1);
}
$rows = $sheet->toArray(null, true, true, true);

for ($i = 3; $i <= 100; $i++) {
    $row = $rows[$i] ?? [];
    $cols = [];
    foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'] as $col) {
        $v = trim((string)($row[$col] ?? ''));
        $cols[] = $v === '' ? '<empty>' : $v;
    }
    echo sprintf('%03d: %s | %s | %s | %s | %s | %s | %s | %s\n', $i, ...$cols);
    if ($i % 10 === 0) {
        echo "---\n";
    }
}
