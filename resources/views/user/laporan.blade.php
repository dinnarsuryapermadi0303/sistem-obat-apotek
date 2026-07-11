@extends('layout')

@section('content')

<div class="container py-5">

    {{-- Alert --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Header --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center gy-3">
                <div class="col-md-8">
                    <h2 class="fw-bold text-primary mb-2">
                        <i class="bi bi-file-earmark-medical"></i>
                        Laporan Validasi Obat
                    </h2>
                    <p class="text-muted mb-0">
                        Riwayat rekomendasi obat yang pernah Anda kirim ke Admin.
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('rekomendasi') }}" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-plus-circle me-2"></i>
                        Rekomendasi Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    @php
    $total = count($laporan);
    $approved = collect($laporan)->where('admin_status', 'Disetujui Admin')->count();
    $pending = collect($laporan)->where('admin_status', 'Menunggu Validasi')->count();
    $rejected = collect($laporan)->where('admin_status', 'Ditolak Admin')->count();
    @endphp

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body text-center py-4">
                    <h6 class="text-muted mb-2">Total Laporan</h6>
                    <h2 class="fw-bold text-primary mb-0">{{ $total }}</h2>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center py-4">
                    <h6 class="text-muted mb-2">Disetujui</h6>
                    <h2 class="fw-bold text-success mb-0">{{ $approved }}</h2>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center py-4">
                    <h6 class="text-muted mb-2">Menunggu</h6>
                    <h2 class="fw-bold text-warning mb-0">{{ $pending }}</h2>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center py-4">
                    <h6 class="text-muted mb-2">Ditolak</h6>
                    <h2 class="fw-bold text-danger mb-0">{{ $rejected }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            @if(count($laporan) === 0)
            <div class="text-center py-5">
                <div class="mb-4" style="width:160px;height:160px;border-radius:50%;background:#eef2ff;color:#2563eb;display:inline-flex;align-items:center;justify-content:center;">
                    <i class="bi bi-clipboard-check fs-1"></i>
                </div>
                <h4 class="fw-bold mb-2">Belum Ada Riwayat</h4>
                <p class="text-muted mb-4">Silakan lakukan rekomendasi obat terlebih dahulu.</p>
                <a href="{{ route('rekomendasi') }}" class="btn btn-primary btn-lg px-4">
                    Mulai Rekomendasi
                </a>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 border rounded-4 overflow-hidden">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th scope="col" class="border-top-0">No</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Keluhan</th>
                            <th scope="col">Obat</th>
                            <th scope="col">Similarity</th>
                            <th scope="col">Confidence</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($laporan as $index => $item)
                        <tr>
                            <td class="fw-semibold">{{ $index + 1 }}</td>
                            <td>{{ $item['tanggal'] ?? '-' }}</td>
                            <td class="text-truncate" style="max-width: 240px;">{{ $item['keluhan'] ?? '-' }}</td>
                            <td><strong>{{ $item['obat'] ?? '-' }}</strong></td>
                            <td>
                                @php
                                $similarityValue = $item['similarity'] ?? 0;
                                if (is_string($similarityValue)) {
                                $similarityValue = str_replace(',', '.', trim($similarityValue));
                                }
                                if (!is_numeric($similarityValue)) {
                                $similarityValue = 0;
                                }
                                $similarityValue = (float) $similarityValue;
                                @endphp
                                <span class="badge bg-info text-dark">{{ number_format($similarityValue, 2) }}%</span>
                            </td>
                            <td>
                                @php $confidence = $item['confidence'] ?? 'Low'; @endphp
                                @if($confidence === 'High')
                                <span class="badge bg-success">High</span>
                                @elseif($confidence === 'Medium')
                                <span class="badge bg-warning text-dark">Medium</span>
                                @else
                                <span class="badge bg-secondary">Low</span>
                                @endif
                            </td>
                            <td>
                                @php $status = $item['admin_status'] ?? 'Menunggu Validasi'; @endphp
                                @if($status === 'Disetujui Admin')
                                <span class="badge bg-success">{{ $status }}</span>
                                @elseif($status === 'Ditolak Admin')
                                <span class="badge bg-danger">{{ $status }}</span>
                                @else
                                <span class="badge bg-warning text-dark">{{ $status }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group" aria-label="Aksi laporan">
                                    <a href="{{ route('laporan.detail', $item['key']) }}" class="btn btn-sm btn-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($item['pdf_ready'] ?? false)
                                    <a href="{{ route('pdf.preview', $item['key']) }}" class="btn btn-sm btn-warning" target="_blank" title="Preview PDF">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    <a href="{{ route('pdf.download', $item['key']) }}" class="btn btn-sm btn-success" title="Download PDF">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    @endif
                                    <form action="{{ route('laporan.delete', $item['key']) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus laporan ini?');" class="d-inline ms-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 20px;
    }

    .table th {
        font-weight: 600;
        vertical-align: middle;
        letter-spacing: 0.01em;
    }

    .table td {
        vertical-align: middle;
    }

    .table thead th {
        border-bottom-width: 2px;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.04);
    }

    .btn-group .btn {
        border-radius: 8px !important;
        margin-left: 0.15rem;
    }

    .badge {
        font-size: 0.85rem;
        padding: 0.55rem 0.75rem;
    }

    .alert {
        border-radius: 16px;
    }

    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>

@endsection