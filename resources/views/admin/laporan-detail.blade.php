@extends('layout-admin')

@section('content')

<div class="container py-5">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-1">Detail Laporan Admin</h2>
            <p class="text-muted mb-0">Lihat detail laporan seperti tampilan pengguna.</p>
        </div>
        <a href="{{ route('admin.laporan') }}" class="btn btn-outline-secondary btn-lg">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h5 class="fw-semibold mb-4">Data Pengguna</h5>
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Tanggal</dt>
                        <dd class="col-7">{{ $detail['tanggal'] ?? '-' }}</dd>

                        <dt class="col-5 text-muted">Nama</dt>
                        <dd class="col-7">{{ $detail['nama'] ?? '-' }}</dd>

                        <dt class="col-5 text-muted">Usia</dt>
                        <dd class="col-7">{{ $detail['usia'] ?? '-' }}</dd>

                        <dt class="col-5 text-muted">Keluhan</dt>
                        <dd class="col-7">{{ $detail['keluhan'] ?? '-' }}</dd>

                        <dt class="col-5 text-muted">Durasi</dt>
                        <dd class="col-7">{{ $detail['durasi'] ?? '-' }}</dd>

                        <dt class="col-5 text-muted">Riwayat</dt>
                        <dd class="col-7">{{ $detail['riwayat'] ?? '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <h5 class="fw-semibold mb-1">Hasil Rekomendasi</h5>
                            <p class="text-muted mb-0">Ringkasan rekomendasi sama seperti tampilan pengguna.</p>
                        </div>
                        @if($detail['pdf_ready'] ?? false)
                        <div class="btn-group" role="group" aria-label="PDF actions">
                            <a href="{{ route('pdf.preview', $detail['key'] ?? $detail['kode'] ?? '') }}" target="_blank" class="btn btn-sm btn-outline-primary">Preview</a>
                            <a href="{{ route('pdf.download', $detail['key'] ?? $detail['kode'] ?? '') }}" class="btn btn-sm btn-primary">Download</a>
                        </div>
                        @endif
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-lg-3">
                            <div class="bg-light rounded-4 p-3 h-100">
                                <div class="text-muted small">Obat Dipilih</div>
                                <div class="fw-semibold">{{ $detail['obat'] ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="bg-light rounded-4 p-3 h-100">
                                <div class="text-muted small">Similarity</div>
                                <div class="fw-semibold">{{ number_format($detail['similarity'] ?? 0, 2) }}%</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="bg-light rounded-4 p-3 h-100">
                                <div class="text-muted small">Confidence</div>
                                <div class="fw-semibold">{{ $detail['confidence'] ?? 'Low' }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="bg-light rounded-4 p-3 h-100">
                                <div class="text-muted small">Status</div>
                                <div>
                                    @php $status = $detail['admin_status'] ?? 'Menunggu Validasi'; @endphp
                                    @if($status === 'Disetujui Admin')
                                    <span class="badge bg-success">{{ $status }}</span>
                                    @elseif($status === 'Ditolak Admin')
                                    <span class="badge bg-danger">{{ $status }}</span>
                                    @else
                                    <span class="badge bg-warning text-dark">{{ $status }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th class="w-25 text-muted">PDF</th>
                                    <td>
                                        @if($detail['pdf_ready'] ?? false)
                                        <span class="badge bg-success">Siap Diunduh</span>
                                        @else
                                        <span class="badge bg-secondary">Belum Tersedia</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Tanggal Disetujui</th>
                                    <td>{{ $detail['approved_at'] ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Obat Yang Disetujui Admin</h5>
                    @if(!empty($detail['approved_meds']))
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($detail['approved_meds'] as $obat)
                        <span class="badge bg-primary px-3 py-2">{{ $obat }}</span>
                        @endforeach
                    </div>
                    @else
                    <div class="alert alert-warning rounded-4 mb-0">
                        Belum ada obat yang disetujui oleh Admin.
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Catatan Admin</h5>
                    <div class="p-3 bg-light rounded-4">
                        {{ $detail['admin_conditions'] ?? 'Belum ada catatan dari Admin.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection