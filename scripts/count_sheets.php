<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$path = __DIR__ . '/../storage/app/Daftar obat Keras dan obat Umum.xlsx';
$spreadsheet = IOFactory::load($path);
foreach ($spreadsheet->getAllSheets() as $sheet) {
    $title = $sheet->getTitle();
    $rows = $sheet->toArray(null, true, true, true);
    $dataCount = 0;
    $rowCount = count($rows);
    foreach ($rows as $i => $row) {
        if ($i <= 3) continue;
        $nonBlank = false;
        foreach ($row as $cell) {
            if (trim((string)$cell) !== '') {
                $nonBlank = true;
                break;
            }
        }
        if ($nonBlank) $dataCount++;
    }
    echo "Sheet: $title, total rows=$rowCount, data rows=$dataCount\n";
}
