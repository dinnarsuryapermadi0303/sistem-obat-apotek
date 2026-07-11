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
$maxCols = 15;
for ($i = 1; $i <= 15; $i++) {
    $row = $rows[$i] ?? [];
    echo sprintf("ROW %02d:\n", $i);
    foreach (range('A', chr(ord('A') + $maxCols - 1)) as $col) {
        $val = trim((string)($row[$col] ?? ''));
        if ($val !== '') {
            echo sprintf("  %-2s = %s\n", $col, $val);
        }
    }
}
