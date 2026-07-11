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
$method = new ReflectionMethod(ExcelHelper::class, 'readSheetData');
$method->setAccessible(true);

foreach ($spreadsheet->getAllSheets() as $sheet) {
    $title = $sheet->getTitle();
    $rows = $sheet->toArray();
    $data = $method->invoke(null, $rows, $title);
    $counts = ['Obat Keras' => 0, 'Obat Umum' => 0, 'Unknown' => 0];
    foreach ($data as $item) {
        $cat = $item['kategori'] ?? 'Unknown';
        if (!isset($counts[$cat])) {
            $counts[$cat] = 0;
        }
        $counts[$cat]++;
    }
    echo "Sheet '{$title}': total parsed=" . count($data) . "\n";
    foreach ($counts as $cat => $c) {
        echo "  {$cat}: {$c}\n";
    }
    echo "First 10 items from sheet {$title}:\n";
    foreach (array_slice($data, 0, 10) as $idx => $item) {
        echo sprintf("%02d: %s | %s | %s\n", $idx + 1, $item['nama'] ?? '', $item['kategori'] ?? '', $item['harga'] ?? '');
    }
    echo "---\n";
}
