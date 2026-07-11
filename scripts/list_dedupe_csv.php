<?php
$files = glob(__DIR__ . '/../storage/app/dedupe_removed_*.csv');
if (!$files) {
    echo "No dedupe CSV found\n";
} else {
    foreach ($files as $f) {
        echo "$f\n";
    }
}
