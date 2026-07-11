<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Helpers\ExcelHelper;

$path = __DIR__ . '/../storage/app/Daftar obat Keras dan obat Umum.xlsx';
$ss = IOFactory::load($path);
$sheet = $ss->getSheetByName('Obat Keras');
$rows = $sheet->toArray();

// find header row
$refIsHeader = new ReflectionMethod(ExcelHelper::class, 'isHeaderRow');
$refIsHeader->setAccessible(true);
$refGetHeaderMapping = new ReflectionMethod(ExcelHelper::class, 'getHeaderMapping');
$refGetHeaderMapping->setAccessible(true);
$headerRow = null;
$mapping = [];
for ($i = 0; $i < count($rows); $i++) {
    $clean = array_map(fn($c) => strtolower(trim((string)$c)), $rows[$i]);
    if ($refIsHeader->invoke(null, $clean)) {
        if ($headerRow === null) {
            $headerRow = $i;
            $mapping = $refGetHeaderMapping->invoke(null, $clean);
            break;
        }
    }
}

$idx = 16; // row idx we saw
$row = $rows[$idx];
print_r(['headerRow' => $headerRow, 'mapping' => $mapping]);
print_r($row);
$deskripsi = trim((string)($row[$mapping['deskripsi'] ?? 5] ?? ''));
$refExtract = new ReflectionMethod(ExcelHelper::class, 'extractNamaFromRow');
$refExtract->setAccessible(true);
$res = $refExtract->invoke(null, $row, $mapping, $deskripsi, 'Obat Keras');
var_export($res);
echo "\n";
