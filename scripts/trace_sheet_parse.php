<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Helpers\ExcelHelper;

$path = __DIR__ . '/../storage/app/Daftar obat Keras dan obat Umum.xlsx';
if (!file_exists($path)) {
    echo "Excel file not found: {$path}\n";
    exit(1);
}

$spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load($path);

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
    echo "\nTracing sheet: {$title}\n";

    $headerRow = null;
    $mapping = [];
    $titleCategory = null;
    $included = [];
    $skipped = [];

    for ($i = 0; $i < count($rows); $i++) {
        $row = $rows[$i];
        $clean = array_map(fn($cell) => strtolower(trim((string)$cell)), $row);
        $isHeader = $refIsHeader->invoke(null, $clean);
        if ($isHeader) {
            $headerRow = $i;
            $mapping = $refGetHeaderMapping->invoke(null, $clean);
            $titleCategory = $refResolveCategory->invoke(null, $title, $rows, $i);
            echo "Header at row {$i}, mapping: " . json_encode($mapping) . ", titleCategory={$titleCategory}\n";
            continue;
        }

        if ($headerRow === null || $i === $headerRow) {
            continue;
        }

        $obat = [
            'nama' => $refGetValue->invoke(null, $row, $mapping, 'nama', 1),
            'kategori_raw' => $refGetValue->invoke(null, $row, $mapping, 'kategori', 0),
            'indikasi' => $refGetValue->invoke(null, $row, $mapping, 'indikasi', 2),
            'dosis' => $refGetValue->invoke(null, $row, $mapping, 'dosis', 3),
            'jenis' => $refGetValue->invoke(null, $row, $mapping, 'jenis', 4),
            'efek_samping' => $refGetValue->invoke(null, $row, $mapping, 'efek_samping', 5),
            'deskripsi' => $refGetValue->invoke(null, $row, $mapping, 'deskripsi', 6),
            'harga' => $refGetValue->invoke(null, $row, $mapping, 'harga', 7),
        ];

        if (empty($obat['nama'])) {
            $obat['nama'] = $refExtractNama->invoke(null, $row, $mapping, $obat['deskripsi'], $title);
        }

        $namaLower = strtolower(trim($obat['nama']));
        $labelKeywords = ['bebas', 'obat keras', 'obat umum', 'tidak ada', 'keterangan', 'no', 'nama'];
        $isLabel = in_array($namaLower, $labelKeywords, true);

        // Consider the row meaningful if any raw cell is non-empty (matching count_sheets logic).
        $hasMeaningful = false;
        foreach ($row as $cell) {
            if (trim((string)$cell) !== '') {
                $hasMeaningful = true;
                break;
            }
        }

        $includedFlag = false;
        if (!$isLabel && $hasMeaningful) {
            $includedFlag = true;
            $included[] = ['row' => $i, 'nama' => $obat['nama'], 'harga' => $obat['harga']];
        } else {
            $skipped[] = ['row' => $i, 'nama' => $obat['nama'], 'harga' => $obat['harga'], 'isLabel' => $isLabel, 'hasMeaningful' => $hasMeaningful];
        }
    }

    echo "Included count: " . count($included) . "\n";
    echo "Skipped count: " . count($skipped) . "\n";
    echo "First 10 included:\n";
    foreach (array_slice($included, 0, 10) as $it) {
        echo " row {$it['row']}: name='{$it['nama']}', harga='{$it['harga']}'\n";
    }
    echo "First 10 skipped:\n";
    foreach (array_slice($skipped, 0, 10) as $it) {
        echo " row {$it['row']}: name='{$it['nama']}', harga='{$it['harga']}', isLabel=" . ($it['isLabel'] ? 1 : 0) . ", hasMeaningful=" . ($it['hasMeaningful'] ? 1 : 0) . "\n";
    }
}
