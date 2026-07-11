<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Helpers\ExcelHelper;
use PhpOffice\PhpSpreadsheet\IOFactory;

$path = __DIR__ . '/../Daftar obat Keras dan obat Umum.xlsx';
if (!file_exists($path)) {
    echo "File not found: {$path}\n";
    exit(1);
}

$spreadsheet = IOFactory::load($path);
$worksheet = $spreadsheet->getActiveSheet();
$rows = $worksheet->toArray();

$header = array_map(fn($cell) => strtolower(trim((string) $cell)), $rows[2] ?? []);
echo "HEADER CLEAN: " . json_encode($header) . "\n";

$reflector = new ReflectionClass(ExcelHelper::class);
$method = $reflector->getMethod('getHeaderMapping');
$method->setAccessible(true);
$map = $method->invoke(null, $header);

echo "MAPPING: " . json_encode($map) . "\n";

foreach ($header as $index => $value) {
    echo "HEADER[{$index}] = [{$value}]\n";
}

echo "--- candidate debug ---\n";
foreach (['deskripsi', 'indikasi', 'dosis', 'jenis', 'efek_samping', 'cara_pakai', 'harga', 'gambar'] as $field) {
    $names = (new ReflectionClass(ExcelHelper::class))->getMethod('getHeaderMapping')->invoke(null, $header);
    echo "FIELD {$field} initially? " . (isset($map[$field]) ? $map[$field] : 'n/a') . "\n";
}

$method2 = $reflector->getMethod('getValue');
$method2->setAccessible(true);
$sampleRow = $rows[3];

echo "SAMPLE ROW KEYS: " . json_encode(array_keys($sampleRow)) . "\n";
echo "SAMPLE ROW VALUES: " . json_encode(array_values($sampleRow)) . "\n";
foreach (['nama', 'kategori', 'deskripsi', 'indikasi', 'efek_samping', 'cara_pakai', 'harga', 'gambar'] as $field) {
    $val = $method2->invoke(null, $sampleRow, $map, $field, 0);
    $mapped = $map[$field] ?? 'n/a';
    $exists = isset($sampleRow[$mapped]) ? 'yes' : 'no';
    echo "FIELD {$field} mapped={$mapped} exists={$exists} => [{$val}]\n";
}

echo "--- parsed data sample ---\n";
$parsed = ExcelHelper::readObatData($path);
echo "PARSED COUNT: " . count($parsed) . "\n";
for ($i = 0; $i < min(3, count($parsed)); $i++) {
    echo "ROW " . ($i + 1) . ": " . json_encode($parsed[$i], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
}
