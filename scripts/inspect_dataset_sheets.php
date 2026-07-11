<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$path = __DIR__ . '/../storage/app/Daftar obat Keras dan obat Umum.xlsx';
if (!file_exists($path)) {
    echo "Excel file not found: {$path}\n";
    exit(1);
}

$spreadsheet = IOFactory::load($path);
$sheetNames = $spreadsheet->getSheetNames();

echo "SheetNames: " . implode(', ', $sheetNames) . "\n";

foreach ($spreadsheet->getAllSheets() as $sheet) {
    $title = $sheet->getTitle();
    $rows = $sheet->toArray();
    $count = count($rows);
    echo "Sheet '{$title}' rows={$count}\n";
    $headerRow = $rows[0] ?? [];
    $headerClean = array_map(fn($cell) => strtolower(trim((string)$cell)), $headerRow);
    echo "Header: " . implode(' | ', $headerClean) . "\n";
    for ($i = 0; $i < min(10, $count); $i++) {
        $row = array_map(fn($cell) => trim((string)$cell), $rows[$i]);
        echo sprintf("%03d: %s\n", $i + 1, implode(' | ', $row));
    }
    echo "---\n";
}
