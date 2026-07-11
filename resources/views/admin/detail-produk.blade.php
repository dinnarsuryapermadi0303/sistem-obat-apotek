@extends('layout-admin')

@section('content')

<div class="container py-5 mt-4">

    <div class="row">

        <div class="col-lg-5 mb-4">

            <div class="card border-0 shadow rounded-4 overflow-hidden">

                @php
                $isKeras = trim($item['kategori'] ?? '') === 'Obat Keras';
                $logoBg = $isKeras ? 'bg-danger' : 'bg-success';
                $logoIcon = $isKeras ? 'bi-shield-lock' : 'bi-heart-pulse';
                $logoLabel = $isKeras ? 'Obat Keras' : 'Obat Umum';
                @endphp

                <div class="d-flex align-items-center justify-content-center {{ $logoBg }} text-white" style="height:500px;">
                    <div class="text-center">
                        <i class="bi {{ $logoIcon }} fs-1"></i>
                        <h4 class="mt-3 mb-1">{{ $logoLabel }}</h4>
                        <p class="mb-0">{{ $item['nama'] }}</p>
                    </div>
                </div>

            </div>

        </div>

        <div class="col-lg-7">

            <div class="card border-0 shadow rounded-4">

                <div class="card-body p-5">

                    <span class="badge bg-primary mb-3">

                        {{ $item['kategori'] }}

                    </span>

                    <h2 class="fw-bold mb-3">

                        {{ $item['nama'] }}

                    </h2>

                    {{-- jenis under title removed per UI change --}}

                    <hr>

                    <div class="row mb-4">

                        <div class="col-md-6">

                            <h6 class="fw-bold">Kategori</h6>

                            <p>{{ $item['kategori'] }}</p>

                        </div>

                        {{-- second column removed (Jenis moved to Penyajian below) --}}

                    </div>

                    <div class="mb-4">

                        <h5 class="fw-bold">Indikasi</h5>

                        <p class="text-secondary">{{ $item['indikasi'] }}</p>

                    </div>

                    <div class="mb-4">

                        <h5 class="fw-bold">Deskripsi</h5>

                        <p class="text-secondary">{{ $item['deskripsi'] }}</p>

                    </div>

                    <div class="mb-4">

                        <h5 class="fw-bold">Penyajian</h5>

                        <p class="text-secondary">{{ $item['jenis'] ?? '-' }}</p>

                    </div>

                    <div class="mb-4">

                        <h5 class="fw-bold">Dosis</h5>

                        <p class="text-secondary">{{ $item['dosis'] ?? '-' }}</p>

                    </div>

                    <div class="mb-4">

                        <h5 class="fw-bold">Efek Samping</h5>

                        <p class="text-secondary">{{ $item['efek_samping'] ?? '-' }}</p>

                    </div>

                    {{-- Kontraindikasi removed per request --}}

                    <div class="mt-4 d-flex gap-2">
                        <a href="/admin/produk" class="btn btn-outline-primary">← Kembali</a>
                        <a href="/admin/produk/{{ $item['id'] }}/edit" class="btn btn-warning">Edit Produk</a>
                        <form action="/admin/produk/{{ $item['id'] }}/hapus" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                            @csrf
                            <button class="btn btn-danger">Hapus Produk</button>
                        </form>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection