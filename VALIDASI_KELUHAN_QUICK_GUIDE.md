# Quick Reference - Validasi Input Keluhan

## Sistem Validasi Berlapis (2 Layer)

### Layer 1: Format Validation

Memastikan input memenuhi kriteria format dasar (tidak asal-asalan)

### Layer 2: Semantic Validation ⭐ NEW

Memastikan input terkait dengan kesehatan/obat (bukan random topic)

---

## Apa yang Divalidasi?

Input **"Keluhan"** di form rekomendasi obat harus:

1. Memenuhi format yang valid (Layer 1)
2. Terkait dengan kesehatan/obat (Layer 2)

---

## Layer 1 - Format Validation

### ❌ Penolakan Format

- Terlalu pendek (< 3 karakter)
- Karakter berulang (aaaa, hhhh)
- Kata berulang (halo halo halo)
- Greeting asal (halo, hai, ok, wkwk)
- Hanya angka/simbol (12345, !!!@@@)
- Kata asal-asalan (a a a)

### ✅ Penerimaan Format

- Minimal 3 karakter bermakna
- Mengandung minimal 1 huruf
- Tidak ada pola asal-asalan

---

## Layer 2 - Semantic Validation ⭐ NEW

### ❌ Penolakan Semantic

```
Pesan: "Keluhan tidak ditemukan"

Alasan: Tidak ada keyword kesehatan/obat

Contoh:
"mobil motor sepeda"       → Transport
"beli buku di toko"        → Shopping
"laptop komputer internet" → Technology
"siapa nama kamu"          → Personal
"cuaca hari ini bagus"     → Weather
```

### ✅ Penerimaan Semantic

```
Harus mengandung minimal 1 kata kesehatan dari:

Gejala: batuk, demam, sakit, nyeri, pusing, kepala, perut,
        mual, muntah, diare, flu, pilek, bersin, gatal, ruam, dll

Penyakit: asma, bronkitis, maag, diabetes, hipertensi,
          jantung, stroke, kanker, infeksi, jamur, dll

Obat: antibiotik, antivirus, vitamin, suplemen, obat, dll

Sistem Tubuh: mata, telinga, gigi, paru, hati, ginjal,
              usus, lambung, otot, tulang, sendi, dll
```

---

## Contoh Valid Input

### Format ✓ + Semantic ✓

- "batuk"
- "demam"
- "sakit kepala"
- "gatal-gatal di tangan"
- "batuk kering 3 hari"
- "nyeri perut setelah makan"
- "butuh obat untuk pilek"

### Format ✗ atau Semantic ✗

```
Format Error:
"halo"              → Terlalu pendek & kata asal
"aaaa"              → Karakter berulang
"ok ok ok"          → Kata berulang

Semantic Error:
"mobil motor"       → Tidak ada health keyword
"beli buku"         → Tidak ada health keyword
"cuaca bagus"       → Tidak ada health keyword
```

---

## Implementation

### Frontend (User Form)

- Real-time validation saat mengetik
- Visual feedback (hijau valid, merah invalid)
- Button submit disable jika invalid
- File: `resources/views/user/rekomendasi.blade.php`

### Backend (Server Validation)

- Format validation → error message khusus
- Semantic validation → pesan: "Keluhan tidak ditemukan"
- File: `app/Http/Controllers/RecommendationController.php`

### Admin Form

- Validasi sama di form edit laporan
- File: `resources/views/admin/laporan-edit.blade.php`

---

## Test Status

✅ **18/18 Test Passed**

- 11 format validation tests
- 7 semantic validation tests ⭐ NEW

```bash
php artisan test tests/Unit/KeluhanValidationTest.php
```

---

## File Berubah

- `app/Http/Controllers/RecommendationController.php` (+ format + semantic validation)
- `resources/views/user/rekomendasi.blade.php` (+ real-time validation)
- `resources/views/admin/laporan-edit.blade.php` (+ form validation)
- `tests/Unit/KeluhanValidationTest.php` (18 test cases)
