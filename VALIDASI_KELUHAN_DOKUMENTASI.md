# Implementasi Validasi Input Keluhan - Dokumentasi Lengkap

## 📋 Overview

Sistem validasi **berlapis** telah diterapkan untuk mencegah input "keluhan" yang asal-asalan dan tidak bermakna di sistem rekomendasi obat:

1. **Format Validation** - Memastikan input memenuhi kriteria format (tidak asal-asalan)
2. **Semantic Validation** - Memastikan input terkait dengan kesehatan/obat (bukan random)

---

## ✅ Test Results

Semua **18 test case PASSED**:

### Format Validation (11 tests)

```
✓ reject_too_short
✓ reject_repeated_characters
✓ reject_repeated_words
✓ reject_meaningless_greeting
✓ reject_only_numbers
✓ reject_special_chars_only
✓ accept_valid_symptom
✓ accept_multiple_symptoms
✓ accept_symptom_with_duration
✓ accept_detailed_symptom
✓ accept_minimum_length
```

### Semantic Validation (7 tests)

```
✓ semantic_reject_random_text (mobil motor sepeda)
✓ semantic_reject_unrelated_content (beli buku di toko)
✓ semantic_reject_technology_keywords (laptop komputer internet)
✓ semantic_accept_health_keyword (batuk)
✓ semantic_accept_symptom_with_unrelated (batuk dan mobil)
✓ semantic_accept_multiple_symptoms (batuk pilek demam)
✓ semantic_accept_obat_keyword (butuh obat untuk sakit)
```

---

## 🔧 Implementasi Teknis

### 1. Backend Validation - Format

**File:** `app/Http/Controllers/RecommendationController.php`

**Method:** `validateKeluhan($keluhan)`

Validasi format input:

```
✓ Minimum 3 karakter
✓ Tidak ada karakter berulang 4+ kali (aaaa, hhhh)
✓ Tidak ada kata berulang >70% (halo halo halo)
✓ Tidak hanya kata-kata asal (halo, hai, ok, wkwk, asdf, dll)
✓ Harus mengandung minimal 1 huruf
✓ Tidak boleh hanya angka/simbol
```

### 2. Backend Validation - Semantic (NEW!)

**File:** `app/Http/Controllers/RecommendationController.php`

**Method:** `validateKeluhanSemantic($keluhan)`

Validasi semantic input:

```
✓ Minimal mengandung 1 kata kesehatan/obat dari dictionary
✓ Dictionary mencakup 100+ keyword kesehatan Indonesia
✓ Kategori: gejala, penyakit, sistem tubuh, jenis obat, kondisi
✓ Jika tidak ada keyword kesehatan → Pesan: "Keluhan tidak ditemukan"
```

**Health Keywords Dictionary:**

```
Gejala: batuk, demam, sakit, nyeri, pusing, kepala, perut, mual,
        muntah, diare, sembelit, flu, pilek, bersin, gatal, ruam, dll

Penyakit: asma, bronkitis, pneumonia, maag, gastritis, diabetes,
          hipertensi, jantung, stroke, kanker, infeksi, jamur, dll

Obat: antibiotik, antivirus, antifungi, vitamin, suplemen,
      ibuprofen, paracetamol, aspirin, CTM, antihistamin, dll

Sistem Tubuh: mata, telinga, gigi, paru, jantung, hati, ginjal,
              usus, lambung, otot, tulang, sendi, dll
```

---

### 3. Frontend Validation - Real-time

**File 1:** `resources/views/user/rekomendasi.blade.php`
**File 2:** `resources/views/admin/laporan-edit.blade.php`

**Fitur Real-time:**

- ✅ Validasi saat user mengetik (input event listener)
- ✅ Visual feedback (hijau valid, merah invalid)
- ✅ Pesan error spesifik membantu user
- ✅ Submit button otomatis disable jika invalid
- ✅ Form submission juga divalidasi

---

## 📝 Contoh Input

### ❌ DITOLAK - Format Validation

```
"halo"              → Terlalu pendek & kata asal
"aaaa"              → Karakter berulang
"halo halo halo"    → Kata berulang
"12345"             → Hanya angka
"!!!@@##"           → Hanya simbol
"ok yes no"         → Greeting asal-asalan
```

### ❌ DITOLAK - Semantic Validation (NEW!)

```
"mobil motor sepeda"       → Tidak ada keyword kesehatan
"beli buku di toko"        → Tidak ada keyword kesehatan
"laptop komputer internet" → Tidak ada keyword kesehatan
"siapa nama kamu"          → Tidak ada keyword kesehatan
"cuaca hari ini bagus"     → Tidak ada keyword kesehatan
"kapal pesawat kereta"     → Tidak ada keyword kesehatan
```

### ✅ DITERIMA - Both Validation Pass

```
"batuk"                          ✓ Format OK + keyword "batuk"
"demam tinggi"                   ✓ Format OK + keyword "demam"
"sakit kepala 2 hari"            ✓ Format OK + keyword "sakit", "kepala"
"gatal-gatal di tangan"          ✓ Format OK + keyword "gatal"
"butuh obat untuk batuk"         ✓ Format OK + keyword "obat", "batuk"
"pilek dan bersin selama 1 minggu" ✓ Format OK + keyword "pilek", "bersin"
```

---

## 🎯 User Flow

```
User Input (keluhan)
    ↓
Format Validation
    ↓ (Jika error format)
    └─→ Pesan: "Keluhan terlalu pendek", "Karakter berulang", dll
    ↓ (Jika format OK)
Semantic Validation (NEW!)
    ↓ (Jika tidak ada health keyword)
    └─→ Pesan: "Keluhan tidak ditemukan"
    ↓ (Jika semantic OK)
Rule-Based Filtering
    ↓ (Jika tidak ada hasil)
    └─→ Pesan: "Tidak ditemukan obat yang sesuai"
    ↓ (Jika ada hasil)
TF-IDF + Cosine Similarity
    ↓
Recommendation Results
```

---

## 🔒 Security & UX

### Security

- Backend validation tidak bisa di-bypass dari frontend
- Setiap request divalidasi 2 layer (format + semantic)
- Perlindungan dari injection & manipulasi
- Semantic check mencegah query yang tidak relevan

### User Experience

- Feedback real-time membantu user input yang valid
- Pesan error jelas dan constructive
- Visual cue (warna) memudahkan identifikasi error
- Button disable mencegah submit error
- Less server load - validasi lebih banyak di client

---

## 📁 File yang Dimodifikasi

1. ✏️ `app/Http/Controllers/RecommendationController.php`
    - Method `validateKeluhan()` (~80 lines)
    - Method `validateKeluhanSemantic()` (~120 lines) **NEW**
    - Semantic validation check di `index()` method

2. ✏️ `resources/views/user/rekomendasi.blade.php`
    - Tambah ID pada input field
    - Tambah JavaScript validation real-time

3. ✏️ `resources/views/admin/laporan-edit.blade.php`
    - Tambah form validation untuk admin

4. 🆕 `tests/Unit/KeluhanValidationTest.php`
    - 18 test cases (11 format + 7 semantic)

---

## 🚀 Cara Menjalankan

### Run Tests

```bash
php artisan test tests/Unit/KeluhanValidationTest.php
```

### Expected Output

```
PASS  Tests\Unit\KeluhanValidationTest
✓ 18 tests passed
Duration: 0.38s
```

---

## 📝 Notes

- Validasi case-insensitive (BATUK = batuk = Batuk)
- Whitespace otomatis di-trim
- Support karakter spesifik (gatal-gatal, sakit perut, dll)
- Dictionary bisa di-extend dengan mudah
- Pesan error user-friendly dan helpful
