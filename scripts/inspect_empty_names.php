<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Helpers\ExcelHelper;
use PhpOffice\PhpSpreadsheet\IOFactory;

$path = __DIR__ . '/../storage/app/Daftar obat Keras dan obat Umum.xlsx';
if (!file_exists($path)) {
    echo "Excel not found\n";
    exit(1);
}

$spreadsheet = IOFactory::load($path);

$refIsHeader = new ReflectionMethod(ExcelHelper::class, 'isHeaderRow');
$refIsHeader->setAccessible(true);
$refGetHeaderMapping = new ReflectionMethod(ExcelHelper::class, 'getHeaderMapping');
$refGetHeaderMapping->setAccessible(true);
$refResolveCategory = new ReflectionMethod(ExcelHelper::class, 'resolveSheetCategory');
$refResolveCategory->setAccessible(true);
$refGetValue = new ReflectionMethod(ExcelHelper::class, 'getValue');
$refGetValue->setAccessible(true);
$refExtractNama = new ReflectionMethod(ExcelHelper::class, 'extractNamaFromRow');
$refExtractNama->setAccessible(true);

foreach ($spreadsheet->getAllSheets() as $sheet) {
    $title = $sheet->getTitle();
    $rows = $sheet->toArray();

    echo "Sheet: {$title}\n";
    $headerRow = null;
    $mapping = [];
    $titleCategory = null;
    for ($i = 0; $i < count($rows); $i++) {
        $row = $rows[$i];
        $clean = array_map(fn($c) => strtolower(trim((string)$c)), $row);
        if ($refIsHeader->invoke(null, $clean)) {
            if ($headerRow === null) {
                $headerRow = $i;
                $mapping = $refGetHeaderMapping->invoke(null, $clean);
                $titleCategory = $refResolveCategory->invoke(null, $title, $rows, $i);
            }
            continue;
        }
        if ($headerRow === null || $i === $headerRow) continue;

        $nama = $refGetValue->invoke(null, $row, $mapping, 'nama', 1);
        $deskripsi = $refGetValue->invoke(null, $row, $mapping, 'deskripsi', 6);
        if (trim((string)$nama) === '') {
            echo "Row {$i} (1-based " . ($i + 1) . "):\n";
            foreach ($row as $col => $cell) {
                echo "  Col{$col}: [" . trim((string)$cell) . "]\n";
            }
            echo "  -> deskripsi: [" . trim((string)$deskripsi) . "]\n";
            echo "---\n";
        }
    }
}
