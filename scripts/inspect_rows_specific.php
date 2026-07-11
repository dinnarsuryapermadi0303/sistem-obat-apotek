<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$path = __DIR__ . '/../storage/app/Daftar obat Keras dan obat Umum.xlsx';
if (!file_exists($path)) {
    echo "Excel not found\n";
    exit(1);
}
$spreadsheet = IOFactory::load($path);

$targets = [
    'Obat Keras' => [4],
    'Obat Umum' => [5],
];

foreach ($spreadsheet->getAllSheets() as $sheet) {
    $title = $sheet->getTitle();
    if (!isset($targets[$title])) continue;
    $rows = $sheet->toArray();
    echo "Sheet: {$title}\n";
    foreach ($targets[$title] as $idx) {
        echo "Row index {$idx} (1-based " . ($idx + 1) . "):\n";
        $row = $rows[$idx] ?? null;
        if ($row === null) {
            echo "  (no row)\n";
            continue;
        }
        foreach ($row as $col => $cell) {
            $c = trim((string)$cell);
            echo "  Col{$col}: [{$c}]\n";
        }
    }
}
