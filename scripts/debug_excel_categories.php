<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Helpers\ExcelHelper;
use PhpOffice\PhpSpreadsheet\IOFactory;

$path = __DIR__ . '/../storage/app/Daftar obat Keras dan obat Umum.xlsx';
if (!file_exists($path)) {
    echo "Excel file not found: {$path}\n";
    exit(1);
}

$spreadsheet = IOFactory::load($path);
foreach ($spreadsheet->getAllSheets() as $sheet) {
    $title = $sheet->getTitle();
    $rows = $sheet->toArray();
    $data = ExcelHelper::readSheetData($rows, $title);
    $counts = ['Obat Keras' => 0, 'Obat Umum' => 0, 'Unknown' => 0];
    foreach ($data as $item) {
        $cat = $item['kategori'] ?? 'Unknown';
        if (!isset($counts[$cat])) {
            $counts[$cat] = 0;
        }
        $counts[$cat]++;
    }
    echo "Sheet '{$title}': parsed=" . count($data) . "\n";
    foreach ($counts as $cat => $c) {
        echo "  {$cat}: {$c}\n";
    }
    echo "---\n";
}
