@extends('layout-admin')

@section('content')

<div class="container py-5">

    @if(session('success'))

    <div class="alert alert-success alert-dismissible fade show">

        {{ session('success') }}

        <button class="btn-close" data-bs-dismiss="alert"></button>

    </div>

    @endif

    @if(session('error'))

    <div class="alert alert-danger alert-dismissible fade show">

        {{ session('error') }}

        <button class="btn-close" data-bs-dismiss="alert"></button>

    </div>

    @endif

    @php

    $total = count($laporan);

    $approved = $total;

    $pending = 0;

    $rejected = 0;

    @endphp

    <div class="card border-0 shadow rounded-4 mb-4">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h2 class="fw-bold text-primary">

                        <i class="bi bi-file-earmark-medical"></i>

                        Laporan Admin

                    </h2>

                    <p class="text-muted mb-0">

                        Seluruh riwayat rekomendasi obat yang dikirim oleh pengguna.

                    </p>

                </div>

                <a href="{{ route('admin.dashboard') }}"
                    class="btn btn-primary">

                    <i class="bi bi-arrow-left"></i>

                    Dashboard

                </a>

            </div>

        </div>

    </div>

    <div class="row mb-4">

        <div class="col-md-3">

            <div class="card shadow-sm border-0 rounded-4">

                <div class="card-body text-center">

                    <h6 class="text-muted">

                        Total Laporan

                    </h6>

                    <h2 class="fw-bold text-primary">

                        {{ $total }}

                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card shadow-sm border-0 rounded-4">

                <div class="card-body text-center">

                    <h6 class="text-muted">

                        Disetujui

                    </h6>

                    <h2 class="fw-bold text-success">

                        {{ $approved }}

                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card shadow-sm border-0 rounded-4">

                <div class="card-body text-center">

                    <h6 class="text-muted">

                        Menunggu

                    </h6>

                    <h2 class="fw-bold text-warning">

                        {{ $pending }}

                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card shadow-sm border-0 rounded-4">

                <div class="card-body text-center">

                    <h6 class="text-muted">

                        Ditolak

                    </h6>

                    <h2 class="fw-bold text-danger">

                        {{ $rejected }}

                    </h2>

                </div>

            </div>

        </div>

    </div>

    <div class="card shadow border-0 rounded-4">

        <div class="card-body">

            <div class="row mb-3">

                <div class="col-md-4">

                    <input

                        type="text"

                        id="search"

                        class="form-control"

                        placeholder="Cari nama atau keluhan...">

                </div>

            </div>

            <div class="table-responsive">

                <form id="bulk-delete-form" action="{{ route('admin.laporan.bulk_delete') }}" method="POST">
                    @csrf
                    <div class="mb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <button id="bulk-delete-btn" class="btn btn-danger btn-sm" type="submit" disabled onclick="return confirm('Yakin ingin menghapus laporan terpilih?')">
                                <i class="bi bi-trash me-1"></i> Hapus Terpilih
                            </button>
                        </div>
                        <div>
                            <input type="text" id="search" class="form-control" placeholder="Cari nama atau keluhan..." style="min-width:260px;">
                        </div>
                    </div>

                    <table class="table table-hover align-middle">

                        <thead class="table-primary">

                            <tr>
                                <th style="width:40px"><input type="checkbox" id="select-all"></th>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama</th>
                                <th>Keluhan</th>
                                <th>Obat</th>
                                <th>Similarity</th>
                                <th>Status</th>
                                <th width="220">Aksi</th>
                            </tr>

                        </thead>


                        <tbody id="tableData">
                            @forelse($laporan as $index => $item)

                            <tr>
                                <td>
                                    <input type="checkbox" class="select-row" name="selected[]" value="@if(!empty($item['id']))id:{{ $item['id'] }}@else key:{{ $item['key'] }}@endif">
                                </td>

                                <td>
                                    {{ $index + 1 }}
                                </td>

                                <td>
                                    {{ $item['tanggal'] ?? '-' }}
                                </td>

                                <td>
                                    {{ $item['nama'] ?? '-' }}
                                </td>

                                <td>
                                    {{ \Illuminate\Support\Str::limit($item['keluhan'] ?? '-',40) }}
                                </td>

                                <td>
                                    <strong>{{ $item['obat'] ?? '-' }}</strong>
                                </td>

                                <td>
                                    @php
                                    $sim = 0;
                                    if(isset($item['similarity_pct'])){
                                    $sim = (float) $item['similarity_pct'];
                                    } elseif(isset($item['similarity'])){
                                    $s = (float) $item['similarity'];
                                    $sim = $s > 1 ? $s : $s * 100;
                                    }
                                    @endphp
                                    @php
                                    $simClass = 'bg-info';
                                    if($sim >= 80) {
                                    $simClass = 'bg-success';
                                    } elseif($sim >= 60) {
                                    $simClass = 'bg-warning text-dark';
                                    }
                                    @endphp
                                    <span class="badge {{ $simClass }}">{{ number_format($sim,2) }}%</span>
                                </td>

                                <td>
                                    @php $status = $item['admin_status'] ?? 'Menunggu Validasi'; @endphp
                                    @if($status == 'Disetujui Admin')
                                    <span class="badge bg-success">{{ $status }}</span>
                                    @elseif($status == 'Ditolak Admin')
                                    <span class="badge bg-danger">{{ $status }}</span>
                                    @else
                                    <span class="badge bg-warning text-dark">{{ $status }}</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-secondary btn-sm btn-detail-laporan"
                                            title="Lihat Detail"
                                            data-id="{{ $item['id'] ?? '' }}"
                                            data-key="{{ $item['key'] ?? '' }}"
                                            data-tanggal="{{ $item['tanggal'] ?? '' }}"
                                            data-nama="{{ $item['nama'] ?? '' }}"
                                            data-usia="{{ $item['usia'] ?? '' }}"
                                            data-keluhan="{{ $item['keluhan'] ?? '' }}"
                                            data-durasi="{{ $item['durasi'] ?? '' }}"
                                            data-riwayat="{{ $item['riwayat'] ?? '' }}"
                                            data-obat="{{ $item['obat'] ?? '' }}"
                                            data-similarity="{{ $item['similarity'] ?? 0 }}"
                                            data-confidence="{{ $item['confidence'] ?? 'Medium' }}"
                                            data-admin-status="{{ $item['admin_status'] ?? '' }}"
                                            data-approved-by="{{ $item['approved_by'] ?? '' }}"
                                            data-approved-at="{{ $item['approved_at'] ?? '' }}"
                                            data-bs-toggle="modal" data-bs-target="#detailLaporanModal">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        @if($item['pdf_ready'] ?? false)
                                        <a href="{{ route('pdf.preview',$item['key']) }}" target="_blank" class="btn btn-warning btn-sm" title="Preview PDF">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>

                                        <a href="{{ route('pdf.download',$item['key']) }}" class="btn btn-success btn-sm" title="Download PDF">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        @endif

                                        <button type="button" class="btn btn-info btn-sm btn-print-laporan"
                                            title="Print Laporan"
                                            data-key="{{ $item['key'] ?? '' }}"
                                            data-nama="{{ $item['nama'] ?? '' }}"
                                            data-tanggal="{{ $item['tanggal'] ?? '' }}"
                                            data-usia="{{ $item['usia'] ?? '' }}"
                                            data-keluhan="{{ $item['keluhan'] ?? '' }}"
                                            data-obat="{{ $item['obat'] ?? '' }}"
                                            data-similarity="{{ $item['similarity'] ?? 0 }}">
                                            <i class="bi bi-printer"></i>
                                        </button>

                                        @if(!empty($item['id']))
                                        <a href="{{ route('admin.validasi.edit',$item['id']) }}" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ route('admin.laporan.delete',$item['id']) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus laporan ini?')" class="d-inline">
                                            @csrf
                                            <button class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @else
                                        <form action="{{ route('admin.laporan.delete_backup', $item['key']) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus backup laporan ini?')" class="d-inline">
                                            @csrf
                                            <button class="btn btn-danger btn-sm" title="Hapus Backup">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="9">
                                    <div class="alert alert-warning text-center mb-0">Belum ada data laporan.</div>
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </form>

            </div>

        </div>

    </div>

</div>
<script>
    document.getElementById('search').addEventListener('keyup', function() {

        let keyword = this.value.toLowerCase();

        let rows = document.querySelectorAll('#tableData tr');

        rows.forEach(function(row) {

            let text = row.innerText.toLowerCase();

            if (text.indexOf(keyword) > -1) {

                row.style.display = '';

            } else {

                row.style.display = 'none';

            }

        });

    });
    // Bulk select handling
    (function() {
        const selectAll = document.getElementById('select-all');
        const rows = document.querySelectorAll('.select-row');
        const bulkBtn = document.getElementById('bulk-delete-btn');

        function updateBulkBtn() {
            const any = Array.from(rows).some(r => r.checked);
            if (bulkBtn) bulkBtn.disabled = !any;
        }

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                rows.forEach(r => r.checked = selectAll.checked);
                updateBulkBtn();
            });
        }

        rows.forEach(r => r.addEventListener('change', updateBulkBtn));
    })();
</script>

<style>
    .card {

        border-radius: 18px;

    }

    .table th {

        font-weight: 600;

        vertical-align: middle;

        white-space: nowrap;

    }

    .table td {

        vertical-align: middle;

    }

    .table-hover tbody tr:hover {

        background: #f8fbff;

        transition: .2s;

    }

    .badge {

        padding: 8px 10px;

        font-size: 12px;

    }

    .btn-group .btn {

        margin-right: 3px;

        border-radius: 8px !important;

    }

    .form-control {

        border-radius: 10px;

    }

    .alert {

        border-radius: 12px;

    }
</style>

<!-- Modal Detail Laporan -->
<div class="modal fade" id="detailLaporanModal" tabindex="-1" aria-labelledby="detailLaporanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-primary text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-bold" id="detailLaporanModalLabel">
                    <i class="bi bi-file-earmark-medical me-2"></i>Detail Laporan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <!-- Data Pengguna -->
                <div class="mb-4">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-person-badge me-2"></i>Data Pengguna
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Nama</label>
                            <p id="modalNama" class="fw-semibold mb-0">-</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Usia</label>
                            <p id="modalUsia" class="fw-semibold mb-0">-</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Tanggal Konsultasi</label>
                            <p id="modalTanggal" class="fw-semibold mb-0">-</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Status</label>
                            <p id="modalStatus" class="mb-0">-</p>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Keluhan dan Riwayat -->
                <div class="mb-4">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-heart-pulse me-2"></i>Keluhan & Riwayat
                    </h6>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Keluhan</label>
                        <p id="modalKeluhan" class="mb-0">-</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Durasi</label>
                        <p id="modalDurasi" class="mb-0">-</p>
                    </div>
                    <div>
                        <label class="form-label text-muted small">Riwayat Penyakit</label>
                        <p id="modalRiwayat" class="mb-0">-</p>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Hasil Rekomendasi -->
                <div class="mb-4">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-capsule me-2"></i>Hasil Rekomendasi
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <label class="form-label text-muted small">Obat Dipilih</label>
                                <p id="modalObat" class="fw-bold mb-0 fs-6">-</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <label class="form-label text-muted small">Similarity Score</label>
                                <p id="modalSimilarity" class="fw-bold mb-0 fs-6">-</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <label class="form-label text-muted small">Confidence</label>
                                <p id="modalConfidence" class="fw-bold mb-0 fs-6">-</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <label class="form-label text-muted small">Admin Status</label>
                                <p id="modalAdminStatus" class="mb-0">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Persetujuan Admin -->
                <div>
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-check-circle me-2"></i>Persetujuan Admin
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Disetujui Oleh</label>
                            <p id="modalApprovedBy" class="fw-semibold mb-0">-</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Tanggal Persetujuan</label>
                            <p id="modalApprovedAt" class="fw-semibold mb-0">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top p-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Tutup
                </button>
                <button type="button" id="printLaporanBtn" class="btn btn-info">
                    <i class="bi bi-printer me-2"></i>Print
                </button>
                <a id="viewDetailBtn" href="#" class="btn btn-primary">
                    <i class="bi bi-eye me-2"></i>Lihat Detail Lengkap
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
    // Handle modal population
    document.addEventListener('DOMContentLoaded', function() {
        const detailBtns = document.querySelectorAll('.btn-detail-laporan');
        const printBtns = document.querySelectorAll('.btn-print-laporan');
        const printLaporanBtn = document.getElementById('printLaporanBtn');
        const viewDetailBtn = document.getElementById('viewDetailBtn');
        let currentDetailKey = null;

        detailBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const data = {
                    key: this.getAttribute('data-key'),
                    id: this.getAttribute('data-id'),
                    tanggal: this.getAttribute('data-tanggal'),
                    nama: this.getAttribute('data-nama'),
                    usia: this.getAttribute('data-usia'),
                    keluhan: this.getAttribute('data-keluhan'),
                    durasi: this.getAttribute('data-durasi'),
                    riwayat: this.getAttribute('data-riwayat'),
                    obat: this.getAttribute('data-obat'),
                    similarity: parseFloat(this.getAttribute('data-similarity')) || 0,
                    confidence: this.getAttribute('data-confidence'),
                    adminStatus: this.getAttribute('data-admin-status'),
                    approvedBy: this.getAttribute('data-approved-by'),
                    approvedAt: this.getAttribute('data-approved-at')
                };

                currentDetailKey = data.key;

                // Populate modal
                document.getElementById('modalNama').textContent = data.nama || '-';
                document.getElementById('modalUsia').textContent = data.usia || '-';
                document.getElementById('modalTanggal').textContent = data.tanggal || '-';
                document.getElementById('modalKeluhan').textContent = data.keluhan || '-';
                document.getElementById('modalDurasi').textContent = data.durasi || '-';
                document.getElementById('modalRiwayat').textContent = data.riwayat || '-';
                document.getElementById('modalObat').textContent = data.obat || '-';
                document.getElementById('modalSimilarity').textContent = Math.round(data.similarity) + '%';
                document.getElementById('modalConfidence').textContent = data.confidence || '-';
                document.getElementById('modalApprovedBy').textContent = data.approvedBy || '-';
                document.getElementById('modalApprovedAt').textContent = data.approvedAt || '-';

                // Status badge
                const statusEl = document.getElementById('modalStatus');
                const adminStatusEl = document.getElementById('modalAdminStatus');

                if (data.adminStatus === 'Disetujui Admin') {
                    statusEl.innerHTML = '<span class="badge bg-success">Disetujui Admin</span>';
                    adminStatusEl.innerHTML = '<span class="badge bg-success">Disetujui Admin</span>';
                } else if (data.adminStatus === 'Ditolak Admin') {
                    statusEl.innerHTML = '<span class="badge bg-danger">Ditolak Admin</span>';
                    adminStatusEl.innerHTML = '<span class="badge bg-danger">Ditolak Admin</span>';
                } else {
                    statusEl.innerHTML = '<span class="badge bg-warning text-dark">Menunggu Validasi</span>';
                    adminStatusEl.innerHTML = '<span class="badge bg-warning text-dark">Menunggu Validasi</span>';
                }

                // Set link to detail page
                viewDetailBtn.href = '{{ route("admin.laporan.detail", ":key") }}'.replace(':key', data.key);
            });
        });

        // Print Laporan
        printBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const data = {
                    nama: this.getAttribute('data-nama'),
                    tanggal: this.getAttribute('data-tanggal'),
                    usia: this.getAttribute('data-usia'),
                    keluhan: this.getAttribute('data-keluhan'),
                    obat: this.getAttribute('data-obat'),
                    similarity: this.getAttribute('data-similarity')
                };
                printLaporanData(data);
            });
        });

        printLaporanBtn.addEventListener('click', function() {
            const data = {
                nama: document.getElementById('modalNama').textContent,
                tanggal: document.getElementById('modalTanggal').textContent,
                usia: document.getElementById('modalUsia').textContent,
                keluhan: document.getElementById('modalKeluhan').textContent,
                obat: document.getElementById('modalObat').textContent,
                similarity: document.getElementById('modalSimilarity').textContent
            };
            printLaporanData(data);
        });

        function printLaporanData(data) {
            const printWindow = window.open('', '', 'height=600,width=800');
            const html = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Laporan Rekomendasi Obat</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 20px;
                            line-height: 1.6;
                        }
                        .header {
                            text-align: center;
                            border-bottom: 2px solid #333;
                            padding-bottom: 15px;
                            margin-bottom: 20px;
                        }
                        .header h1 {
                            margin: 0;
                            font-size: 24px;
                        }
                        .header p {
                            margin: 5px 0;
                            color: #666;
                        }
                        .content {
                            margin: 20px 0;
                        }
                        .section {
                            margin-bottom: 20px;
                        }
                        .section h3 {
                            background-color: #f0f0f0;
                            padding: 10px;
                            margin: 10px 0;
                            border-left: 4px solid #007bff;
                        }
                        .data-row {
                            display: flex;
                            margin-bottom: 10px;
                            padding: 8px 0;
                            border-bottom: 1px solid #eee;
                        }
                        .data-label {
                            width: 30%;
                            font-weight: bold;
                            color: #333;
                        }
                        .data-value {
                            width: 70%;
                            color: #666;
                        }
                        .footer {
                            margin-top: 30px;
                            border-top: 2px solid #333;
                            padding-top: 15px;
                            text-align: right;
                            color: #666;
                            font-size: 12px;
                        }
                        @media print {
                            body {
                                margin: 0;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>📋 Laporan Rekomendasi Obat</h1>
                        <p>Apotek Dinar 24</p>
                    </div>
                    
                    <div class="content">
                        <div class="section">
                            <h3>👤 Data Pengguna</h3>
                            <div class="data-row">
                                <div class="data-label">Nama:</div>
                                <div class="data-value">${data.nama || '-'}</div>
                            </div>
                            <div class="data-row">
                                <div class="data-label">Usia:</div>
                                <div class="data-value">${data.usia || '-'} tahun</div>
                            </div>
                            <div class="data-row">
                                <div class="data-label">Tanggal:</div>
                                <div class="data-value">${data.tanggal || '-'}</div>
                            </div>
                        </div>

                        <div class="section">
                            <h3>❤️ Keluhan</h3>
                            <div class="data-row">
                                <div class="data-value" style="width:100%">${data.keluhan || '-'}</div>
                            </div>
                        </div>

                        <div class="section">
                            <h3>💊 Hasil Rekomendasi</h3>
                            <div class="data-row">
                                <div class="data-label">Obat:</div>
                                <div class="data-value"><strong>${data.obat || '-'}</strong></div>
                            </div>
                            <div class="data-row">
                                <div class="data-label">Similarity Score:</div>
                                <div class="data-value">${data.similarity || '0'}%</div>
                            </div>
                        </div>
                    </div>

                    <div class="footer">
                        <p>Dicetak pada: ${new Date().toLocaleString('id-ID')}</p>
                    </div>
                </body>
                </html>
            `;
            printWindow.document.write(html);
            printWindow.document.close();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 250);
        }
    });
</script>