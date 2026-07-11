<?php
$tests = ['Mefenamic Acid', 'Megasonum', 'Metformin', 'Metronidazole', 'Mecobalamin', 'Mual'];
foreach ($tests as $name) {
    echo "Name: $name\n";
    $n = trim($name);
    if ($n === '') {
        echo " empty\n";
        continue;
    }
    $normalized = strtolower(trim(preg_replace('/[^\p{L}\p{N}\s]/u', '', $n)));
    echo " normalized=[$normalized]\n";
    if ($normalized === '') {
        echo " normalized empty => false\n";
        continue;
    }
    if (preg_match('/^\s*(rp|idr)?\s*[\d\.\,]+\s*$/i', $name)) {
        echo " looks like price => false\n";
        continue;
    }
    $looks = preg_match('/^(meng|men|me|mem|ber|untuk|mencegah|meredakan|membantu|mengatasi|mencegah)/i', trim($name)) === 1;
    if ($looks) {
        echo " looks like indication => false\n";
        continue;
    }
    $symptoms = ['mual', 'muntah', 'kembung', 'nyeri', 'nyeri otot', 'sakit perut', 'demam', 'batuk', 'pilek', 'pusing', 'sakit kepala', 'meredakan', 'mengatasi', 'mengencerkan', 'mengobati', 'membantu', 'digunakan'];
    $lower = $normalized;
    foreach ($symptoms as $s) {
        if ($lower === $s) {
            echo " matches symptom exact => false\n";
            goto next;
        }
        if (str_starts_with($lower, $s . ' ')) {
            echo " startswith symptom => false\n";
            goto next;
        }
    }
    echo " has letters? ";
    var_export(preg_match('/[\p{L}]/u', $name));
    echo "\n";
    if (preg_match('/[\p{L}]/u', $name) !== 1) {
        echo " letter check failed => false\n";
        continue;
    }
    if (mb_strlen($name) > 60) {
        echo " too long => false\n";
        continue;
    }
    echo "VALID\n";
    next:
    echo "---\n";
}
