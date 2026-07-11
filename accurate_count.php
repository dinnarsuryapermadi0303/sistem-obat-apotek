<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = __DIR__ . '/obat fix.xlsx';
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();

$rows = $sheet->toArray();

echo "=== FINAL ANALYSIS - OBAT DATA ===\n\n";

// Find both headers
$kerasHeaderRow = null;
$bebasHeaderRow = null;

for($i = 0; $i < count($rows); $i++) {
    $row = $rows[$i];
    if(isset($row[0]) && isset($row[1])) {
        $col0 = strtolower(trim($row[0]));
        $col1 = strtolower(trim($row[1]));
        
        if($col0 === 'no' && $col1 === 'nama') {
            if($kerasHeaderRow === null) {
                $kerasHeaderRow = $i;
                echo "Obat Keras header found at row " . ($i + 1) . "\n";
            } else if($bebasHeaderRow === null) {
                $bebasHeaderRow = $i;
                echo "Obat Umum header found at row " . ($i + 1) . "\n";
            }
        }
    }
}

echo "\n";

// Collect Obat Keras (from first header to second header)
$kerasItems = [];
if($kerasHeaderRow !== null) {
    $endRow = $bebasHeaderRow ?? count($rows);
    for($i = $kerasHeaderRow + 1; $i < $endRow; $i++) {
        $row = $rows[$i];
        
        if(empty($row[0]) && empty($row[1])) continue;
        if(!is_numeric($row[0])) continue;
        
        if(isset($row[1]) && !empty(trim($row[1]))) {
            $kerasItems[] = [
                'no' => intval($row[0]),
                'nama' => trim($row[1]),
                'kegunaan' => $row[2] ?? '',
                'harga' => $row[3] ?? ''
            ];
        }
    }
}

// Collect Obat Umum (from second header to end)
$bebasItems = [];
if($bebasHeaderRow !== null) {
    for($i = $bebasHeaderRow + 1; $i < count($rows); $i++) {
        $row = $rows[$i];
        
        if(empty($row[0]) && empty($row[1])) continue;
        if(!is_numeric($row[0])) continue;
        
        if(isset($row[1]) && !empty(trim($row[1]))) {
            $bebasItems[] = [
                'no' => intval($row[0]),
                'nama' => trim($row[1]),
                'kegunaan' => $row[2] ?? '',
                'harga' => $row[3] ?? ''
            ];
        }
    }
}

echo "OBAT KERAS:\n";
echo "Total items: " . count($kerasItems) . "\n";

if(count($kerasItems) > 0) {
    $nos = array_column($kerasItems, 'no');
    sort($nos);
    echo "NO range: " . min($nos) . " - " . max($nos) . "\n";
    
    $maxNo = max($nos);
    $missing = [];
    for($n = 1; $n <= $maxNo; $n++) {
        if(!in_array($n, $nos)) {
            $missing[] = $n;
        }
    }
    
    echo "Missing NO: " . count($missing) . " items\n";
    if(count($missing) <= 30) {
        echo "Missing values: " . implode(', ', $missing) . "\n";
    }
    
    echo "\n=== Sampel Obat Keras (first 10) ===\n";
    for($i = 0; $i < 10 && $i < count($kerasItems); $i++) {
        echo ($i+1) . ". [NO " . $kerasItems[$i]['no'] . "] " . $kerasItems[$i]['nama'] . "\n";
    }
    
    echo "\n=== Sampel Obat Keras (last 10) ===\n";
    $start = max(0, count($kerasItems) - 10);
    for($i = $start; $i < count($kerasItems); $i++) {
        echo ($i - $start + 1) . ". [NO " . $kerasItems[$i]['no'] . "] " . $kerasItems[$i]['nama'] . "\n";
    }
}

echo "\n\nOBAT UMUM (BEBAS):\n";
echo "Total items: " . count($bebasItems) . "\n";

if(count($bebasItems) > 0) {
    $nos = array_column($bebasItems, 'no');
    sort($nos);
    echo "NO range: " . min($nos) . " - " . max($nos) . "\n";
    
    echo "\n=== Sampel Obat Umum (first 10) ===\n";
    for($i = 0; $i < 10 && $i < count($bebasItems); $i++) {
        echo ($i+1) . ". [NO " . $bebasItems[$i]['no'] . "] " . $bebasItems[$i]['nama'] . "\n";
    }
    
    echo "\n=== Sampel Obat Umum (last 5) ===\n";
    $start = max(0, count($bebasItems) - 5);
    for($i = $start; $i < count($bebasItems); $i++) {
        echo ($i - $start + 1) . ". [NO " . $bebasItems[$i]['no'] . "] " . $bebasItems[$i]['nama'] . "\n";
    }
}

echo "\n\n╔════════════════════════════════════════╗\n";
echo "║         RINGKASAN DATA EXCEL           ║\n";
echo "╠════════════════════════════════════════╣\n";
echo "║ Obat Keras:    " . str_pad(count($kerasItems), 21) . "║\n";
echo "║ Obat Umum:     " . str_pad(count($bebasItems), 21) . "║\n";
echo "║ ────────────────────────────────────── ║\n";
echo "║ TOTAL:         " . str_pad(count($kerasItems) + count($bebasItems), 21) . "║\n";
echo "╚════════════════════════════════════════╝\n";

if(count($kerasItems) > 0) {
    $kerasNos = array_column($kerasItems, 'no');
    $maxKerasNo = max($kerasNos);
    $missingKerasCount = count(array_filter(range(1, $maxKerasNo), function($n) use ($kerasNos) {
        return !in_array($n, $kerasNos);
    }));
    
    echo "\nKeterangan:\n";
    echo "- Obat Keras: NO dari 1 s/d $maxKerasNo (dengan $missingKerasCount NO yang hilang)\n";
    echo "- Actual items yang ada: " . count($kerasItems) . "\n";
}
