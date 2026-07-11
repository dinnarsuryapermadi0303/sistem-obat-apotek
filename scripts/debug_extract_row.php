<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Helpers\ExcelHelper;
use PhpOffice\PhpSpreadsheet\IOFactory;

$path = __DIR__ . '/../storage/app/Daftar obat Keras dan obat Umum.xlsx';
$spreadsheet = IOFactory::load($path);
$sheet = $spreadsheet->getSheetByName('Obat Keras');
$rows = $sheet->toArray();
$targetIndex = 166; // row 167 1-based

$refIsHeader = new ReflectionMethod(ExcelHelper::class, 'isHeaderRow');
$refIsHeader->setAccessible(true);
$refGetHeaderMapping = new ReflectionMethod(ExcelHelper::class, 'getHeaderMapping');
$refGetHeaderMapping->setAccessible(true);
$refResolveCategory = new ReflectionMethod(ExcelHelper::class, 'resolveSheetCategory');
$refResolveCategory->setAccessible(true);
$refExtractNama = new ReflectionMethod(ExcelHelper::class, 'extractNamaFromRow');
$refExtractNama->setAccessible(true);

$headerRow = null;
$mapping = [];
$titleCategory = null;
for ($i = 0; $i <= $targetIndex; $i++) {
    $clean = array_map(fn($c) => strtolower(trim((string)$c)), $rows[$i]);
    if ($refIsHeader->invoke(null, $clean)) {
        if ($headerRow === null) {
            $headerRow = $i;
            $mapping = $refGetHeaderMapping->invoke(null, $clean);
            $titleCategory = $refResolveCategory->invoke(null, 'Obat Keras', $rows, $i);
        }
    }
}

echo "headerRow={$headerRow}, mapping=" . json_encode($mapping) . "\n";
$row = $rows[$targetIndex];
$des = trim((string)($row[$mapping['deskripsi'] ?? 5] ?? ''));
echo "deskripsi: [{$des}]\n";
$nama = $refExtractNama->invoke(null, $row, $mapping, $des, 'Obat Keras');
echo "extractNamaFromRow => [{$nama}]\n";
