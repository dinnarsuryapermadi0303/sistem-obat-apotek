@extends('layout')

@section('content')

<div class="container pt-1 pb-2 rekomendasi-page">
    <style>
        .rekomendasi-page {
            max-width: 1140px;
            margin: 0 auto;
            padding-top: 0;
        }

        .rekomendasi-page .card {
            border: 1px solid rgba(15, 23, 42, .08);
            box-shadow: 0 22px 50px rgba(15, 23, 42, .06);
        }

        .rekomendasi-page .card-body {
            padding: 1rem;
        }

        .rekomendasi-page .section-header h1 {
            font-size: clamp(2rem, 2.3vw, 2.7rem);
            color: #1d4ed8;
        }

        .rekomendasi-page .section-header p {
            color: #475569;
            max-width: 700px;
        }

        .rekomendasi-page .badge.custom-badge {
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }

        .rekomendasi-page .form-control,
        .rekomendasi-page .form-select,
        .rekomendasi-page textarea.form-control {
            border-radius: 1rem;
        }

        .rekomendasi-page .btn-primary {
            border-radius: 1.5rem;
            min-height: 56px;
            padding: 0.95rem 1.6rem;
            font-weight: 600;
        }

        .rekomendasi-page .btn-outline-primary {
            border-radius: 1rem;
        }

        .rekomendasi-page .summary-card {
            background: #f8fafc;
            border-color: #e2e8f0;
        }

        .rekomendasi-page .summary-card h3 {
            font-size: 2rem;
        }

        .rekomendasi-page .recommendation-card {
            transition: transform .24s ease, box-shadow .24s ease;
        }

        .rekomendasi-page .recommendation-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 35px 90px rgba(15, 23, 42, .12);
        }

        .rekomendasi-page .recommendation-card .card-header {
            background: #f8fafc;
            padding: 1.3rem 1.1rem;
        }

        .rekomendasi-page .recommendation-card .card-body {
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
        }

        .rekomendasi-page .recommendation-card .action-row {
            margin-top: auto;
        }

        .rekomendasi-page .progress {
            height: 0.8rem;
            background: rgba(15, 23, 42, .08);
        }

        .rekomendasi-page .result-summary {
            gap: 1rem;
        }

        .rekomendasi-page .result-summary .card {
            min-height: 150px;
        }

        .rekomendasi-page .selected-medicine-panel {
            background: #eff6ff;
            border-color: #dbeafe;
        }

        .rekomendasi-page .disclaimer-panel {
            background: #f8fafc;
            border-color: #e2e8f0;
        }

        /* ==== Keluhan Picker (chip/badge) ==== */
        .rekomendasi-page #keluhan-picker {
            background: #fff;
        }

        .rekomendasi-page .keluhan-option {
            font-weight: 500;
            padding: .55rem .9rem;
            border-radius: 2rem;
            user-select: none;
            transition: all .15s ease;
        }

        .rekomendasi-page .keluhan-option:hover {
            filter: brightness(0.97);
        }

        .rekomendasi-page .keluhan-option.active {
            box-shadow: 0 4px 10px rgba(29, 78, 216, .25);
        }

        @media (max-width: 768px) {
            .rekomendasi-page .card-body {
                padding: 1.25rem;
            }

            .rekomendasi-page .summary-card {
                min-height: auto;
            }
        }
    </style>
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card shadow-sm border-0 rounded-4 mb-2">
                <div class="card-body p-2 p-md-3">
                    <div class="section-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-1">
                        <div>
                            <h1 class="fw-bold text-primary mb-2">Sistem Rekomendasi Obat</h1>
                            <p class="text-muted mb-0">Masukkan data dan keluhan Anda untuk mendapatkan rekomendasi obat yang relevan. Sistem memadukan Rule Based, TF-IDF, dan Cosine Similarity.</p>
                        </div>
                        <span class="badge custom-badge fs-6">Rekomendasi Pengguna</span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-2">
                <div class="card-body p-2 p-md-3">
                    @if(isset($pesan))
                    <div class="alert alert-warning mb-2">{{ $pesan }}</div>
                    @endif

                    <form id="rekomendasi-form" action="{{ route('rekomendasi') }}" method="GET">
                        <div class="row g-1">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama</label>
                                <input type="text" name="nama" class="form-control" value="{{ old('nama', request('nama')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Usia</label>
                                <input type="number" name="usia" class="form-control" value="{{ old('usia', request('usia')) }}" required>
                            </div>

                            <!-- ============================= -->
                            <!-- Keluhan: pilihan chip/badge, maks 6 -->
                            <!-- ============================= -->
                            <div class="col-md-6 position-relative">
                                <label class="form-label fw-semibold">Keluhan <small class="text-muted">(pilih maks. 6)</small></label>
                                @php
                                $keluhanOptions = $keluhanOptions ?? collect([]);
                                $currentKeluhan = old('keluhan', request('keluhan'));
                                // fallback defaults jika DB kosong
                                $uiKeluhanOptions = $keluhanOptions->filter()->values();
                                if ($uiKeluhanOptions->isEmpty()) {
                                $uiKeluhanOptions = collect([
                                'Batuk', 'Pilek', 'Demam', 'Flu', 'Sakit Kepala', 'Pusing', 'Mual', 'Muntah',
                                'Diare', 'Sembelit', 'Nyeri Perut', 'Gatal', 'Ruam Kulit', 'Sesak Napas',
                                'Sakit Tenggorokan', 'Alergi', 'Mata Gatal', 'Hidung Tersumbat', 'Bersin-Bersin',
                                'Dahak Berlebih', 'Masalah Tidur', 'Nyeri Otot'
                                ]);
                                }

                                // Pecah string keluhan yang sudah ada (misal dari request sebelumnya) jadi array
                                $selectedKeluhan = collect(explode(',', $currentKeluhan ?? ''))
                                ->map(fn($k) => trim($k))
                                ->filter()
                                ->values();

                                // Jika ada nilai terpilih yang belum ada di daftar opsi, tambahkan agar tetap terlihat & aktif
                                foreach ($selectedKeluhan as $sk) {
                                if (!$uiKeluhanOptions->contains($sk)) {
                                $uiKeluhanOptions->push($sk);
                                }
                                }
                                @endphp

                                <div id="keluhan-picker" class="d-flex flex-wrap gap-2 p-2 border rounded-3" style="max-height:200px; overflow-y:auto;">
                                    @foreach($uiKeluhanOptions as $option)
                                    <span class="badge keluhan-option {{ $selectedKeluhan->contains($option) ? 'bg-primary text-white active' : 'bg-light text-dark border' }}"
                                        data-value="{{ $option }}" role="button">
                                        {{ $option }}
                                    </span>
                                    @endforeach
                                </div>

                                <div class="input-group mt-3">
                                    <input type="text" id="keluhan-manual-input" class="form-control" placeholder="Tambah keluhan manual" aria-label="Tambah keluhan manual">
                                    <button type="button" id="keluhan-add-manual" class="btn btn-outline-secondary">Tambah</button>
                                </div>

                                <input type="hidden" id="keluhan-input" name="keluhan" value="{{ $selectedKeluhan->implode(', ') }}" required>
                                <small id="keluhan-feedback" class="form-text"></small>
                                <small id="keluhan-counter" class="form-text text-muted mt-1">{{ $selectedKeluhan->count() }}/6 dipilih</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Durasi</label>
                                <input type="text" name="durasi" class="form-control" value="{{ old('durasi', request('durasi')) }}" placeholder="Contoh: 3 Hari">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Riwayat Penyakit</label>
                                <textarea name="riwayat" rows="3" class="form-control" placeholder="Opsional">{{ old('riwayat', request('riwayat')) }}</textarea>
                            </div>
                        </div>

                        <button class="btn btn-primary btn-lg w-100 mt-2" type="submit">
                            <i class="bi bi-search me-2"></i> Proses Rekomendasi
                        </button>
                    </form>
                </div>
            </div>

            <div id="rekomendasi-results">
                @if(isset($hasil) && count($hasil))
                @php
                $maxSimilarity = collect($hasil)->max(fn($item) => $item['persentase'] ?? 0);
                $bestItem = collect($hasil)->sortByDesc(fn($item) => $item['persentase'] ?? 0)->first();
                @endphp

                <div class="row g-1 mb-2 result-summary">
                    <div class="col-sm-4">
                        <div class="card rounded-4 shadow-sm p-2 h-100 summary-card">
                            <small class="text-muted">Total Obat</small>
                            <h3 class="fw-bold mt-3 mb-2">{{ count($hasil) }}</h3>
                            <p class="text-muted mb-0">Jumlah obat yang memenuhi kriteria dari input Anda.</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card rounded-4 shadow-sm p-4 h-100">
                            <small class="text-muted">Top Similarity</small>
                            <h3 class="fw-bold mt-3 mb-2">{{ number_format($maxSimilarity, 0) }}%</h3>
                            <p class="text-muted mb-0">Skor kecocokan tertinggi pada hasil rekomendasi.</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card rounded-4 shadow-sm p-4 h-100">
                            <small class="text-muted">Rekomendasi Utama</small>
                            <h3 class="fw-bold mt-3 mb-2">{{ $bestItem['nama'] ?? '-' }}</h3>
                            <p class="text-muted mb-0">Obat dengan peringkat terbaik dalam daftar rekomendasi.</p>
                        </div>
                    </div>
                </div>

                <div class="row g-2">
                    @foreach($hasil as $item)
                    @php
                    $isKeras = trim($item['kategori'] ?? '') === 'Obat Keras';
                    $logoClass = $isKeras ? 'bg-danger' : 'bg-success';
                    $logoIcon = $isKeras ? 'bi-shield-lock' : 'bi-heart-pulse';
                    $logoLabel = $isKeras ? 'Obat Keras' : 'Obat Umum';
                    // Normalize similarity to percentage (0-100)
                    $persentase = 0;
                    if(isset($item['persentase'])){
                    $persentase = (float) $item['persentase'];
                    } elseif(isset($item['similarity_pct'])){
                    $persentase = (float) $item['similarity_pct'];
                    } elseif(isset($item['similarity'])){
                    $s = (float) $item['similarity'];
                    $persentase = $s > 1 ? $s : $s * 100;
                    }
                    // Determine color class
                    $simClass = 'bg-info';
                    if($persentase >= 80){
                    $simClass = 'bg-success';
                    } elseif($persentase >= 60){
                    $simClass = 'bg-warning text-dark';
                    }
                    @endphp
                    <div class="col-xl-4 col-md-6">
                        <div class="card rounded-4 shadow-sm h-100 overflow-hidden recommendation-card">
                            <div class="p-3 text-center bg-light">
                                <div class="rounded-circle {{ $logoClass }} text-white d-inline-flex align-items-center justify-content-center mb-2" style="width:72px;height:72px;">
                                    <i class="bi {{ $logoIcon }} fs-3"></i>
                                </div>
                                <span class="badge {{ $logoClass }} text-white px-3 py-2">{{ $logoLabel }}</span>
                                <h5 class="fw-bold mt-2 mb-1">{{ $item['nama'] ?? $item['obat']['nama'] ?? '-' }}</h5>
                                <p class="text-muted mb-0">{{ $item['kategori'] ?? '-' }}</p>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-primary">Ranking #{{ $item['ranking'] ?? '-' }}</span>
                                </div>
                                <p class="text-muted small mb-2">{{ \Illuminate\Support\Str::limit($item['deskripsi'] ?? '-', 110) }}</p>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between mb-1 small fw-semibold text-secondary">
                                        <span>Similarity</span>
                                        <span>{{ number_format($persentase,2) }}%</span>
                                    </div>
                                    <div class="progress" style="height:0.75rem;">
                                        <div class="progress-bar {{ $simClass }}" role="progressbar" data-progress="{{ $persentase }}" aria-valuenow="{{ $persentase }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 mb-2">
                                    <span class="badge bg-secondary flex-grow-1 text-truncate">Confidence: {{ $item['confidence'] ?? 'Medium' }}</span>
                                </div>
                                <div class="mt-auto action-row">
                                    <button type="button" class="btn btn-outline-primary w-100 mb-2 btn-view-detail"
                                        data-nama="{{ $item['nama'] ?? $item['obat']['nama'] ?? '' }}"
                                        data-kategori="{{ $item['kategori'] ?? '' }}"
                                        data-indikasi="{{ $item['indikasi'] ?? $item['obat']['indikasi'] ?? '' }}"
                                        data-deskripsi="{{ $item['deskripsi'] ?? $item['obat']['deskripsi'] ?? '' }}"
                                        data-jenis="{{ $item['jenis'] ?? $item['obat']['jenis'] ?? '-' }}"
                                        data-dosis="{{ $item['dosis'] ?? $item['obat']['dosis'] ?? '-' }}"
                                        data-harga="{{ $item['harga'] ?? $item['obat']['harga'] ?? 0 }}"
                                        data-efek_samping="{{ $item['efek_samping'] ?? $item['obat']['efek_samping'] ?? '-' }}"
                                        data-ranking="{{ $item['ranking'] ?? '-' }}"
                                        data-similarity="{{ $persentase }}"
                                        data-confidence="{{ $item['confidence'] ?? 'Medium' }}"
                                        data-bs-toggle="modal" data-bs-target="#detailObatModal">
                                        <i class="bi bi-eye me-1"></i> Detail & Pilih
                                    </button>
                                    <form action="{{ route('rekomendasi.select') }}" method="POST" class="select-obat-form" style="display:none;">
                                        @csrf
                                        <input type="hidden" name="selected_obat" value="{{ $item['nama'] ?? $item['obat']['nama'] ?? '' }}">
                                        <input type="hidden" name="similarity" value="{{ $persentase }}">
                                        <input type="hidden" name="confidence" value="{{ $item['confidence'] ?? 'Medium' }}">
                                        <input type="hidden" name="nama" value="{{ request('nama') }}">
                                        <input type="hidden" name="usia" value="{{ request('usia') }}">
                                        <input type="hidden" name="keluhan" value="{{ request('keluhan') }}">
                                        <input type="hidden" name="durasi" value="{{ request('durasi') }}">
                                        <input type="hidden" name="riwayat" value="{{ request('riwayat') }}">
                                        <button class="btn btn-success w-100" type="submit">
                                            <i class="bi bi-check-circle me-1"></i> Pilih Obat Ini
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="card shadow-sm border-0 rounded-4 mt-2 selected-medicine-panel">
                <div class="card-body p-2">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2">
                        <div>
                            <h5 class="fw-semibold mb-1">Obat yang Dipilih</h5>
                            <p id="selected-medicine-name" class="text-muted mb-0">{{ session('selected_medicine') ?? 'Belum ada obat dipilih' }}</p>
                            <small class="text-muted">Obat akan dikirim ke admin untuk validasi.</small>
                        </div>
                        <div>
                            <form id="validasi-form" action="{{ route('validasi.submit') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="nama" value="{{ request('nama') }}">
                                <input type="hidden" name="usia" value="{{ request('usia') }}">
                                <input type="hidden" name="keluhan" value="{{ request('keluhan') }}">
                                <input type="hidden" name="durasi" value="{{ request('durasi') }}">
                                <input type="hidden" name="riwayat" value="{{ request('riwayat') }}">
                                <input id="validasi_selected_obat" type="hidden" name="selected_obat" value="{{ session('selected_medicine') ?? '' }}">
                                <input id="validasi_similarity" type="hidden" name="similarity" value="{{ session('similarity', 0) }}">
                                <input id="validasi_confidence" type="hidden" name="confidence" value="{{ session('confidence', 'Medium') }}">
                                <button type="submit" id="kirim-validasi-btn" class="btn btn-sm btn-outline-secondary" disabled>Belum ada obat dipilih</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mt-2 mb-3 disclaimer-panel">
                <div class="card-body p-2">
                    <h5 class="fw-semibold mb-3">Disclaimer</h5>
                    <p class="text-muted mb-0">Sistem ini menggunakan metode <strong>Rule Based</strong>, <strong>TF-IDF</strong>, dan <strong>Cosine Similarity</strong> untuk memberikan rekomendasi obat berdasarkan gejala pengguna. Hasil rekomendasi hanya sebagai referensi awal dan tidak menggantikan diagnosis atau konsultasi medis.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Obat untuk Validasi Pemilihan -->
<div class="modal fade" id="detailObatModal" tabindex="-1" aria-labelledby="detailObatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-primary text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-bold" id="detailObatModalLabel">
                    <i class="bi bi-capsule me-2"></i>Detail Obat yang Direkomendasikan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <!-- Card Visual Header -->
                <div class="card border-0 mb-4 overflow-hidden">
                    <div id="modalKategoriHeader" class="d-flex align-items-center justify-content-center bg-success text-white p-5" style="min-height:250px;">
                        <div class="text-center">
                            <i id="modalKategoriIcon" class="bi bi-heart-pulse fs-1 mb-3 d-block"></i>
                            <div id="modalKategoriLabel" class="fw-semibold mb-3">Obat Umum</div>
                            <h4 id="modalObatNama" class="fw-bold">-</h4>
                        </div>
                    </div>
                </div>

                <!-- Detail Informasi Obat -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="fw-bold text-primary mb-2">Kategori</h6>
                            <p id="modalKategori" class="text-secondary mb-0">-</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="fw-bold text-primary mb-2">Ranking</h6>
                            <p id="modalRanking" class="text-secondary mb-0">-</p>
                        </div>
                    </div>
                </div>

                <!-- Indikasi -->
                <div class="mb-4">
                    <h6 class="fw-bold text-primary mb-2">
                        <i class="bi bi-info-circle me-2"></i>Indikasi
                    </h6>
                    <p id="modalIndikasi" class="text-secondary">-</p>
                </div>

                <!-- Deskripsi -->
                <div class="mb-4">
                    <h6 class="fw-bold text-primary mb-2">
                        <i class="bi bi-file-text me-2"></i>Deskripsi
                    </h6>
                    <p id="modalDeskripsi" class="text-secondary">-</p>
                </div>

                <!-- Penyajian -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-primary mb-2">
                            <i class="bi bi-box me-2"></i>Penyajian
                        </h6>
                        <p id="modalJenis" class="text-secondary">-</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-primary mb-2">
                            <i class="bi bi-prescription me-2"></i>Dosis
                        </h6>
                        <p id="modalDosis" class="text-secondary">-</p>
                    </div>
                </div>

                <!-- Harga -->
                <div class="mb-4 p-3 rounded-3 bg-light">
                    <h6 class="fw-bold text-primary mb-2">
                        <i class="bi bi-cash-coin me-2"></i>Harga
                    </h6>
                    <p id="modalHarga" class="text-secondary mb-0 fs-5 fw-semibold">-</p>
                </div>

                <!-- Efek Samping -->
                <div class="mb-4 p-3 rounded-3 bg-light">
                    <h6 class="fw-bold text-primary mb-2">
                        <i class="bi bi-exclamation-octagon me-2"></i>Efek Samping
                    </h6>
                    <p id="modalEfekSamping" class="text-secondary mb-0">-</p>
                </div>

                <!-- Rekomendasi Score -->
                <div class="mb-4 p-3 rounded-3 bg-info bg-opacity-10">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-graph-up me-2"></i>Skor Rekomendasi
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="d-block text-muted mb-2">Similarity Score</small>
                            <div class="d-flex align-items-center gap-2">
                                <span id="modalSimilarity" class="badge bg-info fs-6">0%</span>
                                <div class="progress flex-grow-1" style="height:0.5rem;">
                                    <div id="modalSimilarityBar" class="progress-bar bg-info" role="progressbar" style="width:0%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <small class="d-block text-muted mb-2">Confidence</small>
                            <span id="modalConfidence" class="badge bg-warning text-dark">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top p-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Batal
                </button>
                <button type="button" id="confirmPilihBtn" class="btn btn-success">
                    <i class="bi bi-check-circle me-2"></i>Pilih Obat Ini
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('rekomendasi-form');
        const resultContainer = document.getElementById('rekomendasi-results');
        const submitBtn = form ? form.querySelector('button[type="submit"]') : null;
        const validasiForm = document.getElementById('validasi-form');
        const validasiBtn = document.getElementById('kirim-validasi-btn');
        const detailModal = document.getElementById('detailObatModal');
        const confirmPilihBtn = document.getElementById('confirmPilihBtn');
        let currentSelectedForm = null;

        const showLoading = () => {
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Memproses...';
            }
        };

        const hideLoading = () => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-search me-2"></i> Proses Rekomendasi';
            }
        };

        if (form) {
            form.addEventListener('submit', function(event) {
                const requiredFields = ['nama', 'usia', 'keluhan'];
                let invalid = false;

                requiredFields.forEach(name => {
                    const input = form.querySelector(`[name="${name}"]`);
                    if (input && !input.value.trim()) {
                        invalid = true;
                    }
                });

                if (invalid) {
                    event.preventDefault();
                    alert('Silakan isi semua kolom yang diperlukan');
                    return;
                }

                showLoading();
            });
        }

        // Fungsi untuk menampilkan detail obat di modal
        function populateModalDetail(button) {
            const nama = button.getAttribute('data-nama');
            const kategori = button.getAttribute('data-kategori');
            const indikasi = button.getAttribute('data-indikasi');
            const deskripsi = button.getAttribute('data-deskripsi');
            const jenis = button.getAttribute('data-jenis');
            const dosis = button.getAttribute('data-dosis');
            const harga = parseFloat(button.getAttribute('data-harga')) || 0;
            const efekSamping = button.getAttribute('data-efek_samping');
            const ranking = button.getAttribute('data-ranking');
            const similarity = parseFloat(button.getAttribute('data-similarity')) || 0;
            const confidence = button.getAttribute('data-confidence');

            // Set kategori header background dan icon
            const isKeras = kategori === 'Obat Keras';
            const headerBg = isKeras ? 'bg-danger' : 'bg-success';
            const headerIcon = isKeras ? 'bi-shield-lock' : 'bi-heart-pulse';
            const modalHeader = document.getElementById('modalKategoriHeader');
            const modalIcon = document.getElementById('modalKategoriIcon');
            const modalLabel = document.getElementById('modalKategoriLabel');

            if (modalHeader) {
                modalHeader.className = `d-flex align-items-center justify-content-center ${headerBg} text-white p-5`;
                modalHeader.style.minHeight = '250px';
            }
            if (modalIcon) {
                modalIcon.className = `bi ${headerIcon} fs-1 mb-3 d-block`;
            }
            if (modalLabel) {
                modalLabel.textContent = kategori || 'Tanpa Kategori';
            }

            // Set detail information
            document.getElementById('modalObatNama').textContent = nama || '-';
            document.getElementById('modalKategori').textContent = kategori || '-';
            document.getElementById('modalIndikasi').textContent = indikasi || '-';
            document.getElementById('modalDeskripsi').textContent = deskripsi || '-';
            document.getElementById('modalJenis').textContent = jenis || '-';
            document.getElementById('modalDosis').textContent = dosis || '-';
            document.getElementById('modalHarga').textContent = 'Rp ' + new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0
            }).format(harga);
            document.getElementById('modalEfekSamping').textContent = efekSamping || '-';
            document.getElementById('modalRanking').textContent = ranking || '-';
            document.getElementById('modalSimilarity').textContent = Math.round(similarity) + '%';
            document.getElementById('modalSimilarityBar').style.width = similarity + '%';
            document.getElementById('modalConfidence').textContent = confidence || '-';
        }

        // Attach listeners untuk tombol detail
        function attachDetailListeners() {
            const detailBtns = document.querySelectorAll('.btn-view-detail');
            detailBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    populateModalDetail(this);
                    // Store reference ke form yang terkait untuk nanti
                    const formParent = this.closest('.card-body');
                    currentSelectedForm = formParent.querySelector('.select-obat-form');
                });
            });
        }

        // Tombol confirm di modal
        if (confirmPilihBtn) {
            confirmPilihBtn.addEventListener('click', function() {
                if (currentSelectedForm) {
                    handleSelectForm({
                        currentTarget: currentSelectedForm,
                        preventDefault: () => {}
                    });
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(detailModal);
                    if (modal) modal.hide();
                }
            });
        }

        function attachFormListeners() {
            const selectForms = document.querySelectorAll('.select-obat-form');
            selectForms.forEach(form => {
                form.removeEventListener('submit', handleSelectForm);
                form.addEventListener('submit', handleSelectForm);
            });
        }

        function handleSelectForm(e) {
            e.preventDefault();
            const form = e.currentTarget;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Memilih...';
            }

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const selectedObat = formData.get('selected_obat');
                        const selectedSimilarity = formData.get('similarity');
                        const selectedConfidence = formData.get('confidence');
                        const selectedName = document.querySelector('#selected-medicine-name');
                        const hiddenObat = document.querySelector('#validasi_selected_obat');
                        const hiddenSimilarity = document.querySelector('#validasi_similarity');
                        const hiddenConfidence = document.querySelector('#validasi_confidence');

                        if (selectedName) selectedName.textContent = selectedObat || 'Belum ada obat dipilih';
                        if (hiddenObat) hiddenObat.value = selectedObat || '';
                        if (hiddenSimilarity) hiddenSimilarity.value = selectedSimilarity ?? 0;
                        if (hiddenConfidence) hiddenConfidence.value = selectedConfidence ?? 'Medium';

                        if (validasiBtn) {
                            validasiBtn.disabled = false;
                            validasiBtn.className = 'btn btn-sm btn-success';
                            validasiBtn.textContent = 'Kirim Validasi';
                        }

                        // Show success toast
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                        alertDiv.style.zIndex = '9999';
                        alertDiv.innerHTML = `
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>Sukses!</strong> Obat "${selectedObat}" telah dipilih.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        document.body.appendChild(alertDiv);
                        setTimeout(() => alertDiv.remove(), 4000);
                        // auto-submit validation so the selected obat is saved to laporan
                        submitValidationAutomatically();
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memilih obat. Silakan coba lagi.');
                })
                .finally(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Pilih Obat Ini';
                    }
                });
        }

        attachFormListeners();
        attachDetailListeners();

        // Submit validation automatically after selection so it appears in laporan
        function submitValidationAutomatically() {
            if (!validasiForm) return;

            const formData = new FormData(validasiForm);

            fetch(validasiForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(resp => resp.json())
                .then(json => {
                    if (json && json.success) {
                        // redirect to laporan to show the saved report
                        window.location.href = '{{ route("laporan") }}';
                    } else {
                        if (json && json.message) console.warn('Validation save failed:', json.message);
                    }
                })
                .catch(err => {
                    console.error('Error submitting validation:', err);
                });
        }

        // =============================
        // Pemilihan Keluhan (chip/badge, maks 6)
        // =============================
        const keluhanPicker = document.getElementById('keluhan-picker');
        const keluhanInput = document.getElementById('keluhan-input');
        const keluhanFeedback = document.getElementById('keluhan-feedback');
        const keluhanCounter = document.getElementById('keluhan-counter');
        const MAX_KELUHAN = 6;

        function getSelectedKeluhan() {
            if (!keluhanPicker) return [];
            return Array.from(keluhanPicker.querySelectorAll('.keluhan-option.active')).map(el => el.dataset.value);
        }

        function syncKeluhanInput() {
            const selected = getSelectedKeluhan();
            if (keluhanInput) keluhanInput.value = selected.join(', ');

            if (keluhanCounter) {
                keluhanCounter.textContent = `${selected.length}/${MAX_KELUHAN} dipilih`;
                keluhanCounter.className = selected.length === MAX_KELUHAN ?
                    'form-text fw-semibold text-danger mt-1' :
                    'form-text text-muted mt-1';
            }

            if (!keluhanFeedback) return;

            if (selected.length === 0) {
                keluhanFeedback.textContent = '⚠️ Pilih minimal 1 keluhan.';
                keluhanFeedback.className = 'form-text text-danger fw-semibold';
                if (submitBtn) submitBtn.disabled = true;
            } else {
                keluhanFeedback.textContent = '✓ Keluhan dipilih.';
                keluhanFeedback.className = 'form-text text-success fw-semibold';
                if (submitBtn) submitBtn.disabled = false;
            }
        }

        const keluhanManualInput = document.getElementById('keluhan-manual-input');
        const keluhanAddManualBtn = document.getElementById('keluhan-add-manual');

        if (keluhanPicker) {
            keluhanPicker.addEventListener('click', function(e) {
                const opt = e.target.closest('.keluhan-option');
                if (!opt) return;

                const isActive = opt.classList.contains('active');
                const selectedCount = getSelectedKeluhan().length;

                if (!isActive && selectedCount >= MAX_KELUHAN) {
                    if (keluhanFeedback) {
                        keluhanFeedback.textContent = `⚠️ Maksimal ${MAX_KELUHAN} keluhan yang bisa dipilih.`;
                        keluhanFeedback.className = 'form-text text-danger fw-semibold';
                    }
                    return;
                }

                opt.classList.toggle('active');
                opt.classList.toggle('bg-primary');
                opt.classList.toggle('text-white');
                opt.classList.toggle('bg-light');
                opt.classList.toggle('text-dark');
                opt.classList.toggle('border');

                syncKeluhanInput();
            });

            // Tampilkan status awal (misalnya saat kembali dari hasil pencarian sebelumnya)
            syncKeluhanInput();
        }

        function addManualKeluhan(value) {
            const text = String(value || '').trim();
            if (!text) return;
            const labels = text.split(',').map(v => v.trim()).filter(Boolean);
            const currentSelected = getSelectedKeluhan();
            const availableCount = MAX_KELUHAN - currentSelected.length;
            if (labels.length > availableCount) {
                if (keluhanFeedback) {
                    keluhanFeedback.textContent = `⚠️ Anda hanya bisa memilih ${availableCount} keluhan lagi.`;
                    keluhanFeedback.className = 'form-text text-danger fw-semibold';
                }
                return;
            }

            labels.forEach(label => {
                let option = Array.from(keluhanPicker.querySelectorAll('.keluhan-option')).find(el => el.dataset.value.toLowerCase() === label.toLowerCase());
                if (!option) {
                    option = document.createElement('span');
                    option.className = 'badge keluhan-option bg-primary text-white active';
                    option.dataset.value = label;
                    option.setAttribute('role', 'button');
                    option.textContent = label;
                    keluhanPicker.appendChild(option);
                } else if (!option.classList.contains('active')) {
                    option.classList.add('active', 'bg-primary', 'text-white');
                    option.classList.remove('bg-light', 'text-dark', 'border');
                }
            });

            syncKeluhanInput();
            if (keluhanManualInput) keluhanManualInput.value = '';
        }

        if (keluhanAddManualBtn) {
            keluhanAddManualBtn.addEventListener('click', function() {
                addManualKeluhan(keluhanManualInput ? keluhanManualInput.value : '');
            });
        }

        if (keluhanManualInput) {
            keluhanManualInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addManualKeluhan(keluhanManualInput.value);
                }
            });
        }

        // Validasi saat submit form utama
        document.getElementById('rekomendasi-form').addEventListener('submit', function(e) {
            if (getSelectedKeluhan().length === 0) {
                e.preventDefault();
                alert('Silakan pilih minimal 1 keluhan.');
                return false;
            }
        });
    });
</script>

@endsection