@extends('layout')

@section('content')

@php
// Normalize similarity to percentage and compute display variables
$sim = 0;
if(isset($similarity)){
$s = (float) $similarity;
$sim = $s > 1 ? $s : $s * 100;
}

if($sim >= 80) {
$warna = 'success';
$confidence = 'High';
} elseif($sim >= 60) {
$warna = 'primary';
$confidence = 'Medium';
} elseif($sim >= 40) {
$warna = 'warning';
$confidence = 'Low';
} else {
$warna = 'danger';
$confidence = 'Very Low';
}

// similarity color class for progress/badge
$simClass = 'bg-info';
if($sim >= 80){
$simClass = 'bg-success';
} elseif($sim >= 60){
$simClass = 'bg-warning text-dark';
}

$isKeras = trim($obat['kategori'] ?? '') === 'Obat Keras';
$logoBg = $isKeras ? 'bg-danger' : 'bg-success';
$logoIcon = $isKeras ? 'bi-shield-lock' : 'bi-heart-pulse';
$logoLabel = $isKeras ? 'Obat Keras' : 'Obat Umum';
@endphp

<div class="container py-5">
    <div class="row justify-content-center gx-4 gy-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="row g-0 align-items-stretch">
                    <div class="col-md-4 {{ $logoBg }} text-white d-flex align-items-center justify-content-center p-4">
                        <div class="text-center">
                            <div class="rounded-circle bg-white bg-opacity-10 p-4 mb-3 d-inline-flex align-items-center justify-content-center" style="width:100px;height:100px;">
                                <i class="bi {{ $logoIcon }} fs-2"></i>
                            </div>
                            <h5 class="mb-1">{{ $logoLabel }}</h5>
                            <p class="mb-0 small opacity-75">{{ $obat['kategori'] }}</p>
                        </div>
                    </div>
                    <div class="col-md-8 bg-white">
                        <div class="card-body p-4">
                            <span class="badge bg-success px-3 py-2 rounded-pill">HASIL REKOMENDASI</span>
                            <h2 class="fw-bold mt-3 mb-2">{{ $obat['nama'] }}</h2>
                            <p class="text-muted mb-3">Rekomendasi obat yang paling sesuai untuk keluhan Anda berdasarkan proses analisis.</p>
                            <div class="row g-3">
                                <div class="col-sm-4">
                                    <div class="card border-0 bg-light rounded-4 p-3 h-100">
                                        <small class="text-muted">Similarity</small>
                                        <h4 class="fw-bold mb-1">{{ number_format($sim, 2) }}%</h4>
                                        <div class="progress" style="height:0.5rem;">
                                            <div class="progress-bar {{ $simClass }}" role="progressbar" data-progress="{{ $sim }}" aria-valuenow="{{ $sim }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="card border-0 bg-light rounded-4 p-3 h-100">
                                        <small class="text-muted">Confidence</small>
                                        <h4 class="fw-bold mb-1">{{ $confidence }}</h4>
                                        <p class="mb-0 text-muted">Level kecocokan sistem.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-0 bg-light rounded-4 mt-4">
                                <div class="card-body">
                                    <h5 class="fw-semibold mb-3">Informasi Singkat</h5>
                                    <p class="text-muted mb-0">{{ \Illuminate\Support\Str::limit($obat['deskripsi'] ?? '-', 180) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4">
                    <h4 class="fw-semibold mb-3">Informasi Obat</h4>
                    <dl class="row mb-0">
                        <dt class="col-4 text-muted">Nama Obat</dt>
                        <dd class="col-8">{{ $obat['nama'] }}</dd>

                        <dt class="col-4 text-muted">Kategori</dt>
                        <dd class="col-8">{{ $obat['kategori'] }}</dd>



                        <dt class="col-4 text-muted">Indikasi</dt>
                        <dd class="col-8">{{ $obat['indikasi'] ?? '-' }}</dd>

                        <dt class="col-4 text-muted">Deskripsi</dt>
                        <dd class="col-8">{{ $obat['deskripsi'] ?? '-' }}</dd>

                        <dt class="col-4 text-muted">Penyajian</dt>
                        <dd class="col-8">{{ $obat['jenis'] ?? '-' }}</dd>

                        <dt class="col-4 text-muted">Dosis</dt>
                        <dd class="col-8">{{ $obat['dosis'] ?? '-' }}</dd>

                        <dt class="col-4 text-muted">Harga</dt>
                        <dd class="col-8">
                            @php
                            $hargaValue = $obat['harga'] ?? null;
                            $displayHarga = is_numeric($hargaValue) ? 'Rp ' . number_format((int)$hargaValue, 0, ',', '.') : ($hargaValue ?: '-');
                            @endphp
                            {{ $displayHarga }}
                        </dd>

                        <dt class="col-4 text-muted">Komposisi</dt>
                        <dd class="col-8">{{ $obat['komposisi'] ?? '-' }}</dd>

                        <dt class="col-4 text-muted">Kontraindikasi</dt>
                        <dd class="col-8">{{ $obat['kontraindikasi'] ?? '-' }}</dd>

                        <dt class="col-4 text-muted">Efek Samping</dt>
                        <dd class="col-8">{{ $obat['efek_samping'] ?? '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3">Ringkasan Rekomendasi</h5>
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th class="text-muted">Nama Pengguna</th>
                                <td>{{ $nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Usia</th>
                                <td>{{ $usia ?? '-' }} Tahun</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Keluhan</th>
                                <td>{{ $query ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Durasi</th>
                                <td>{{ $durasi ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Riwayat</th>
                                <td>{{ $riwayat ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Obat Terpilih</th>
                                <td><strong>{{ $obat['nama'] }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Similarity</th>
                                <td>{{ number_format($sim, 2) }}%</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Confidence</th>
                                <td>{{ $confidence }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tanggal</th>
                                <td>{{ $display_time ?? now()->format('d F Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3">Validasi Admin</h5>
                    <div class="alert alert-warning mb-0">
                        <p class="mb-0">Setelah Anda memilih rekomendasi ini, sistem menyimpan hasil rekomendasi dan mengirimkannya ke Admin untuk pemeriksaan. Hasil validasi akan menentukan apakah obat sesuai dengan keluhan pengguna.</p>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4">
                    <form action="{{ route('validasi.submit') }}" method="POST">
                        @csrf
                        <input type="hidden" name="nama" value="{{ $nama ?? '' }}">
                        <input type="hidden" name="usia" value="{{ $usia ?? '' }}">
                        <input type="hidden" name="keluhan" value="{{ $query ?? '' }}">
                        <input type="hidden" name="durasi" value="{{ $durasi ?? '' }}">
                        <input type="hidden" name="riwayat" value="{{ $riwayat ?? '' }}">
                        <input type="hidden" name="selected_obat" value="{{ $obat['nama'] }}">
                        <input type="hidden" name="similarity" value="{{ $sim }}">
                        <button type="submit" class="btn btn-success btn-lg w-100">✔ Pilih & Kirim ke Validasi</button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3">Laporan PDF</h5>
                    @php $validasiStatus = session('status_validasi','Pending'); @endphp
                    @if($validasiStatus == 'Approved')
                    <div class="d-grid gap-3">
                        <a href="{{ route('pdf.preview', $key ?: $obat['nama']) }}" target="_blank" class="btn btn-primary">👁 Preview PDF</a>
                        <a href="{{ route('pdf.download', $key ?: $obat['nama']) }}" class="btn btn-success">⬇ Download PDF</a>
                    </div>
                    @else
                    <div class="alert alert-info mb-0">PDF akan tersedia setelah Admin menyetujui rekomendasi ini.</div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3">Proses Sistem</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">✅ Keluhan diproses menggunakan TF-IDF.</li>
                        <li class="list-group-item">✅ Similarity dihitung menggunakan Cosine Similarity.</li>
                        <li class="list-group-item">✅ Sistem menghasilkan ranking obat.</li>
                        <li class="list-group-item">✅ User memilih obat.</li>
                        <li class="list-group-item">⏳ Menunggu validasi Admin.</li>
                        <li class="list-group-item">📄 PDF dibuat setelah Admin menyetujui.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-12 mt-4">
            <a href="{{ route('rekomendasi', [
                'nama' => $nama ?? '',
                'usia' => $usia ?? '',
                'keluhan' => $query ?? '',
                'durasi' => $durasi ?? '',
                'riwayat' => $riwayat ?? '',
            ]) }}" class="btn btn-outline-secondary">← Kembali ke Rekomendasi</a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.progress-bar[data-progress]').forEach(function(bar) {
            bar.style.width = bar.dataset.progress + '%';
        });
    });
</script>

@endsection