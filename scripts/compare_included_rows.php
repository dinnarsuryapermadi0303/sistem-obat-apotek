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
$method = new ReflectionMethod(ExcelHelper::class, 'readSheetData');
$method->setAccessible(true);

foreach ($spreadsheet->getAllSheets() as $sheet) {
    $title = $sheet->getTitle();
    $rows = $sheet->toArray();

    // data rows per count_sheets logic: skip first 3 rows (0-based indices 0..2)
    $dataIndices = [];
    for ($i = 0; $i < count($rows); $i++) {
        if ($i <= 2) continue; // skip first 3 rows
        $row = $rows[$i];
        $nonBlank = false;
        foreach ($row as $cell) {
            if (trim((string)$cell) !== '') {
                $nonBlank = true;
                break;
            }
        }
        if ($nonBlank) $dataIndices[] = $i;
    }

    // parser-included indices by re-running readSheetData logic but capturing which row indices were included
    $includedIndices = [];
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

    $headerRow = null;
    $mapping = [];
    $titleCategory = null;
    for ($i = 0; $i < count($rows); $i++) {
        $row = $rows[$i];
        $clean = array_map(fn($cell) => strtolower(trim((string)$cell)), $row);
        $isHeader = $refIsHeader->invoke(null, $clean);
        if ($isHeader) {
            if ($headerRow === null) {
                $headerRow = $i;
                $mapping = $refGetHeaderMapping->invoke(null, $clean);
                $titleCategory = $refResolveCategory->invoke(null, $title, $rows, $i);
            }
            continue;
        }
        if ($headerRow === null || $i === $headerRow) continue;

        $obat = [
            'nama' => $refGetValue->invoke(null, $row, $mapping, 'nama', 1),
            'deskripsi' => $refGetValue->invoke(null, $row, $mapping, 'deskripsi', 6),
            'harga' => $refGetValue->invoke(null, $row, $mapping, 'harga', 7),
        ];
        if (empty($obat['nama'])) $obat['nama'] = $refExtractNama->invoke(null, $row, $mapping, $obat['deskripsi'], $title);

        $labelKeywords = ['bebas', 'obat keras', 'obat umum', 'tidak ada', 'keterangan', 'no', 'nama'];
        $namaLower = strtolower(trim($obat['nama']));
        $isLabel = in_array($namaLower, $labelKeywords, true);

        // meaningful: any raw cell non-empty
        $hasMeaningful = false;
        foreach ($row as $cell) {
            if (trim((string)$cell) !== '') {
                $hasMeaningful = true;
                break;
            }
        }

        if (!$isLabel && $hasMeaningful) {
            $includedIndices[] = $i;
        }
    }

    echo "Sheet: {$title}\n";
    echo "  dataIndices count=" . count($dataIndices) . "; includedIndices count=" . count($includedIndices) . "\n";
    $diff1 = array_diff($dataIndices, $includedIndices);
    $diff2 = array_diff($includedIndices, $dataIndices);
    echo "  in dataIndices but not included: ";
    echo json_encode(array_values($diff1)) . "\n";
    echo "  in included but not in dataIndices: ";
    echo json_encode(array_values($diff2)) . "\n";
}
