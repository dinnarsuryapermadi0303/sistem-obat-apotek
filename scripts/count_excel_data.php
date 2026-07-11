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
$sheetNames = $spreadsheet->getSheetNames();
$activeSheet = $spreadsheet->getActiveSheet();
$rows = $activeSheet->toArray();

echo "Sheet count: " . count($sheetNames) . "\n";
echo "Sheet names: " . implode(', ', $sheetNames) . "\n";
echo "Total raw rows: " . count($rows) . "\n";

echo "\nFirst 40 rows:\n";
for ($i = 0; $i < min(40, count($rows)); $i++) {
    $row = array_map(fn($cell) => trim((string)$cell), $rows[$i]);
    echo sprintf("%03d: %s\n", $i + 1, implode(' | ', $row));
}

echo "\nParsing using ExcelHelper...\n";
$data = ExcelHelper::readObatData($path);
echo "Parsed items: " . count($data) . "\n";

$counts = [
    'Obat Keras' => 0,
    'Obat Umum' => 0,
];
foreach ($data as $item) {
    $category = $item['kategori'] ?? 'Unknown';
    if (!isset($counts[$category])) {
        $counts[$category] = 0;
    }
    $counts[$category]++;
}

echo "Category counts:\n";
foreach ($counts as $category => $count) {
    echo "  {$category}: {$count}\n";
}

echo "\nFirst 20 parsed items:\n";
foreach (array_slice($data, 0, 20) as $index => $item) {
    echo sprintf("%03d: %s | %s | %s\n", $index + 1, $item['nama'] ?? '', $item['kategori'] ?? '', $item['kategori_raw'] ?? '');
}
