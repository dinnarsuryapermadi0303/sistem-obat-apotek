@extends('layout')

@section('content')

<div class="container py-5 mt-5">

    <div class="row">

        {{-- Logo Obat --}}
        <div class="col-lg-5 mb-4">

            <div class="card border-0 shadow rounded-4 overflow-hidden">

                @php
                $kategoriValue = trim($obat['kategori'] ?? '');
                $isKeras = $kategoriValue === 'Obat Keras';
                $isUmum = $kategoriValue === 'Obat Umum';
                $logoBg = $isKeras ? 'bg-danger' : ($isUmum ? 'bg-success' : 'bg-primary');
                $logoIcon = $isKeras ? 'bi-shield-lock' : ($isUmum ? 'bi-heart-pulse' : 'bi-tags');
                $logoLabel = $kategoriValue ?: 'Tanpa Kategori';
                $badgeClass = $isKeras ? 'bg-danger bg-opacity-10 text-danger' : ($isUmum ? 'bg-success bg-opacity-10 text-success' : 'bg-primary bg-opacity-10 text-primary');
                @endphp

                <div class="d-flex align-items-center justify-content-center {{ $logoBg }} text-white" style="height:500px;">
                    <div class="text-center">
                        <i class="bi {{ $logoIcon }} fs-1"></i>
                        <h4 class="mt-3 mb-1">{{ $logoLabel }}</h4>
                        <p class="mb-0">{{ $obat['nama'] }}</p>
                    </div>
                </div>

            </div>

        </div>

        {{-- Informasi Obat --}}
        <div class="col-lg-7">

            <div class="card border-0 shadow rounded-4">

                <div class="card-body p-5">

                    <span class="badge {{ $badgeClass }} mb-3">
                        {{ $obat['kategori'] ?? $logoLabel }}
                    </span>

                    <h2 class="fw-bold mb-3">

                        {{ $obat['nama'] }}

                    </h2>

                    {{-- jenis under title removed per UI change --}}

                    <hr>

                    <div class="row mb-4">

                        <div class="col-md-6">

                            <h6 class="fw-bold">

                                Kategori

                            </h6>

                            <p>

                                {{ $obat['kategori'] }}

                            </p>

                        </div>

                        {{-- second column removed (Jenis moved to Penyajian below) --}}

                    </div>

                    <div class="mb-4">

                        <h5 class="fw-bold">

                            Indikasi

                        </h5>

                        <p class="text-secondary">

                            {{ $obat['indikasi'] }}

                        </p>

                    </div>

                    <div class="mb-4">

                        <h5 class="fw-bold">

                            Deskripsi

                        </h5>

                        <p class="text-secondary">

                            {{ $obat['deskripsi'] }}

                        </p>

                    </div>

                    <div class="mb-4">

                        <h5 class="fw-bold">

                            Penyajian

                        </h5>

                        <p class="text-secondary">

                            {{ $obat['jenis'] ?? '-' }}

                        </p>

                    </div>

                    <div class="mb-4">

                        <h5 class="fw-bold">

                            Dosis

                        </h5>

                        <p class="text-secondary">

                            {{ $obat['dosis'] ?? '-' }}

                        </p>

                    </div>

                    <div class="mb-4">

                        <h5 class="fw-bold">

                            Harga

                        </h5>

                        <p class="text-secondary">
                            @php
                            $hargaValue = $obat['harga'] ?? null;
                            @endphp
                            {{ is_numeric($hargaValue) && $hargaValue !== null ? 'Rp ' . number_format((int) $hargaValue, 0, ',', '.') : ($hargaValue ?: '-') }}
                        </p>

                    </div>

                    <div class="mb-4">

                        <h5 class="fw-bold">

                            Efek Samping

                        </h5>

                        <p class="text-secondary">

                            {{ $obat['efek_samping'] ?? '-' }}

                        </p>

                    </div>

                    {{-- Kontraindikasi removed per request --}}

                    <div class="mt-5">
                        <a id="back-to-produk" href="#" class="btn btn-outline-primary px-5 rounded-pill">
                            ← Kembali ke Produk
                        </a>
                    </div>

                    @push('scripts')
                    <script>
                        (function() {
                            const back = document.getElementById('back-to-produk');
                            if (!back) return;
                            const params = new URLSearchParams(window.location.search);
                            let cat = params.get('from_kategori') || params.get('kategori');
                            if (cat) {
                                back.href = '/produk?kategori=' + encodeURIComponent(cat);
                                return;
                            }
                            try {
                                const stored = sessionStorage.getItem('produk_kategori');
                                if (stored) {
                                    back.href = '/produk?kategori=' + encodeURIComponent(stored);
                                    return;
                                }
                            } catch (e) {
                                // ignore
                            }
                            back.addEventListener('click', function(e) {
                                e.preventDefault();
                                window.history.back();
                            });
                        })();
                    </script>
                    @endpush

                </div>

            </div>

        </div>

    </div>

</div>

@endsection