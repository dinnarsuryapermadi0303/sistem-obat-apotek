@extends('layout-admin')

@section('content')

<div class="container py-5">

    {{-- HEADER --}}
    <div class="row justify-content-center mb-5">

        <div class="col-xl-10">

            <div class="card dashboard-hero position-relative overflow-hidden mb-4">

                <div class="card-body py-5 px-4">

                    <div class="row align-items-center">

                        <div class="col-lg-8">

                            <span class="dashboard-chip mb-3 d-inline-block">
                                Ringkasan {{ date('Y') }}
                            </span>

                            <h1 class="fw-bold display-6 mb-3">
                                Panel Admin Apotek 24
                            </h1>

                            <p class="mb-4 opacity-85">
                                Kelola data produk, validasi rekomendasi, laporan pengguna, dan pengaturan sistem melalui dashboard administrator.
                            </p>

                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-white text-primary py-2 px-3 rounded-pill shadow-sm">
                                    {{ $totalProduk }} Produk
                                </span>
                                <span class="badge bg-white text-primary py-2 px-3 rounded-pill shadow-sm">
                                    {{ $totalUser }} User
                                </span>
                                <span class="badge bg-white text-primary py-2 px-3 rounded-pill shadow-sm">
                                    {{ $totalRekomendasi }} Rekomendasi
                                </span>
                            </div>

                        </div>

                        <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                            @php
                            $status = $statusWebsite ?? 'Aktif';
                            @endphp
                            <div class="dashboard-chip d-inline-flex align-items-center justify-content-center {{ $status === 'Aktif' ? 'bg-success' : 'bg-danger' }}" style="color:#fff;">
                                Status: {{ $status }}
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- CARD DASHBOARD --}}
    <div class="row g-4">

        <div class="col-lg-4 col-md-6">

            <div class="card dashboard-stat-card border-0 shadow-sm h-100 p-4 text-center">

                <span class="text-muted">Total Produk</span>
                <h2 class="fw-bold mt-3 mb-3">{{ $totalProduk }}</h2>

                <p class="text-muted mb-0">
                    Semua produk aktif dan tersedia di database admin.
                </p>

            </div>

        </div>

        <div class="col-lg-4 col-md-6">

            <div class="card dashboard-stat-card border-0 shadow-sm h-100 p-4 text-center">

                <span class="text-muted">Total User</span>
                <h2 class="fw-bold mt-3 mb-3">{{ $totalUser }}</h2>

                <p class="text-muted mb-0">
                    Jumlah pengguna yang mengajukan rekomendasi.
                </p>

            </div>

        </div>

        <div class="col-lg-4 col-md-6">

            <div class="card dashboard-stat-card border-0 shadow-sm h-100 p-4 text-center">

                <span class="text-muted">Total Rekomendasi</span>
                <h2 class="fw-bold mt-3 mb-3">{{ $totalRekomendasi }}</h2>

                <p class="text-muted mb-0">
                    Jumlah rekomendasi obat yang dibuat oleh pengguna.
                </p>

            </div>

        </div>

    </div>

    {{-- MENU ADMIN --}}
    <div class="row mt-5 g-4">

        {{-- KELOLA PRODUK --}}
        <div class="col-lg-4 col-md-6">

            <div class="card dashboard-info-card h-100 p-4 text-center">

                <div class="mb-4">
                    <div class="fs-2 text-primary mb-3">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Kelola Produk</h4>
                    <p class="text-muted mb-3">Tambah, edit, dan hapus produk obat.</p>
                </div>

                <a href="{{ Route::has('admin.produk') ? route('admin.produk') : '#' }}" class="btn btn-outline-primary btn-sm">
                    Buka Produk
                </a>

            </div>

        </div>

        {{-- LAPORAN --}}
        <div class="col-lg-4 col-md-6">

            <div class="card dashboard-info-card h-100 p-4 text-center">

                <div class="mb-4">
                    <div class="fs-2 text-success mb-2">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Laporan</h4>
                    <p class="text-muted mb-0">Analisis data rekomendasi dan insight pengguna.</p>
                </div>

                <a href="{{ Route::has('admin.laporan') ? route('admin.laporan') : '#' }}" class="btn btn-outline-primary btn-sm">
                    Lihat Laporan
                </a>

            </div>

        </div>

        {{-- PENGATURAN --}}
        <div class="col-lg-4 col-md-6">

            <div class="card dashboard-info-card h-100 p-4 text-center">

                <div class="mb-4">
                    <div class="fs-2 text-dark mb-2">
                        <i class="bi bi-gear"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Pengaturan</h4>
                    <p class="text-muted mb-0">Ubah preferensi dan konfigurasi situs.</p>
                </div>

                <a href="{{ Route::has('admin.pengaturan') ? route('admin.pengaturan') : '#' }}" class="btn btn-outline-primary btn-sm">
                    Atur Sistem
                </a>

            </div>

        </div>

    </div>

    {{-- INFO SISTEM --}}
    <div class="card dashboard-info-card mt-5 p-4 text-center">

        <div class="card-body">

            <h4 class="fw-bold mb-3">
                Informasi Sistem
            </h4>

            <p class="text-muted mb-4 mx-auto" style="max-width: 760px; font-size: 1rem; line-height: 1.8;">
                Dashboard admin menampilkan data produk, pengguna, dan rekomendasi secara real-time berdasarkan aktivitas pengguna di sistem.
            </p>

            <ul class="dashboard-list list-unstyled mb-0 mx-auto" style="max-width: 760px; text-align: left;">

                <li>
                    Total produk akan otomatis berubah saat ada penambahan atau penghapusan.
                </li>

                <li>
                    Total user akan bertambah ketika pengguna mengirim permintaan rekomendasi.
                </li>

                <li>
                    Total rekomendasi akan meningkat setiap ada permintaan rekomendasi.
                </li>

                <li>
                    Semua perubahan data tampil langsung di dashboard dan laporan admin.
                </li>

            </ul>

        </div>

    </div>

</div>

@endsection