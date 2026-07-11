<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$path = __DIR__ . '/../storage/app/Daftar obat Keras dan obat Umum.xlsx';
$spreadsheet = IOFactory::load($path);
$sheet = $spreadsheet->getSheetByName('Obat Keras');
$rows = $sheet->toArray();

foreach ($rows as $i => $row) {
    $des = trim((string)($row[5] ?? ''));
    if ($des !== '' && preg_match('/^\s*(kembung|mual|nyeri|pusing)/iu', $des)) {
        echo "Row idx={$i} (1-based=" . ($i + 1) . "):\n";
        foreach ($row as $ci => $c) {
            echo " Col{$ci}: [" . trim((string)$c) . "]\n";
        }
        echo "---\n";
    }
}
