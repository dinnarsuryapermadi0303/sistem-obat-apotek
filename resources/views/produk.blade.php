@extends('layout')

@section('content')

<div class="product-background">
    <div class="container about-section">
        <div class="row align-items-center justify-content-center mb-4 text-center">
            <div class="col-lg-8">
                <h2 class="fw-bold">Daftar Produk Obat</h2>
                <p class="text-muted">Produk obat yang tersedia di Apotek Dinar</p>
            </div>
        </div>

        <div class="row gy-3 mb-5 product-search-bar">
            <div class="col-12">
                <div class="row gy-3 gx-3 justify-content-center text-center">
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-white shadow-sm">
                            <p class="mb-1 text-muted" style="font-size: 0.95rem;">Total Obat</p>
                            <h4 id="total-obat-count" class="mb-0">{{ $totalObat ?? 0 }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background: #f5f7ff;">
                            <p class="mb-1 text-muted" style="font-size: 0.95rem;">Obat Keras</p>
                            <h4 id="obat-keras-count" class="mb-0">{{ $categoryCounts['Obat Keras'] ?? 0 }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background: #effcf6;">
                            <p class="mb-1 text-muted" style="font-size: 0.95rem;">Obat Umum</p>
                            <h4 id="obat-umum-count" class="mb-0">{{ $categoryCounts['Obat Umum'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-8">
                <form action="/produk/search" method="GET">
                    <div class="input-group shadow-sm rounded-pill overflow-hidden">
                        <input type="text" name="q" class="form-control border-0 py-3" placeholder="Cari obat..." value="{{ request('q') }}">
                        <button class="btn btn-primary px-4" type="submit">🔍 Cari</button>
                    </div>
                </form>
            </div>

            <div class="col-12 col-lg-4 d-flex justify-content-lg-end align-items-center">
                <form action="/produk/filter" method="GET">
                    <select name="kategori" class="form-select shadow-sm rounded-pill py-3" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @if(isset($categories) && count($categories) > 0)
                        @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('kategori') == $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                        @endif
                    </select>
                </form>
            </div>
        </div>
    </div>

    <!-- LIST PRODUK -->
    @if(isset($obatList) && count($obatList) > 0)

    <div id="product-list-row" class="row gx-4 gy-4 justify-content-center product-grid-wrapper">

        @foreach($obatList as $obat)
        @php
        $kategoriValue = trim($obat['kategori'] ?? 'Tanpa Kategori');
        $isKeras = $kategoriValue === 'Obat Keras';
        $isUmum = $kategoriValue === 'Obat Umum';
        $logoBg = $isKeras ? 'bg-danger' : ($isUmum ? 'bg-success' : 'bg-primary');
        $logoIcon = $isKeras ? 'bi-shield-lock' : ($isUmum ? 'bi-heart-pulse' : 'bi-tags');
        $badgeClass = $isKeras ? 'bg-danger bg-opacity-10 text-danger' : ($isUmum ? 'bg-success bg-opacity-10 text-success' : 'bg-primary bg-opacity-10 text-primary');
        $hargaValue = $obat['harga'] ?? 0;
        if (!is_int($hargaValue) && !is_float($hargaValue)) {
        $hargaValue = preg_replace('/[^0-9\.]/', '', (string)$hargaValue);
        $hargaValue = $hargaValue === '' ? 0 : (float)$hargaValue;
        }
        @endphp
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8 mb-4 d-flex">

            @php
            $fromCat = request('kategori');
            $detailUrl = url('/obat/' . rawurlencode($obat['nama']));
            if (!empty($fromCat)) {
            $detailUrl .= '?from_kategori=' . rawurlencode($fromCat);
            }
            @endphp
            <a href="{{ $detailUrl }}" style="text-decoration:none; color:inherit; width: 100%;">

                <div class="card text-center h-100 product-card shadow-sm">

                    <div class="card-header {{ $logoBg }} text-white">
                        <div class="text-center">
                            <i class="bi {{ $logoIcon }} fs-1"></i>
                            <div class="mt-3 small fw-semibold product-label">{{ $kategoriValue }}</div>
                        </div>
                    </div>

                    <div class="card-body">
                        <h5 class="fw-bold">{{ $obat['nama'] }}</h5>
                        <p class="text-muted mb-1">{{ $kategoriValue }}</p>
                        <span class="product-badge-small mb-2 {{ $badgeClass }}">{{ $kategoriValue }}</span>
                        <small class="text-muted d-block mb-3">
                            📝 {{ Str::limit($obat['deskripsi'], 60) }}
                        </small>
                        <p class="price">
                            Rp {{ number_format($hargaValue, 0, ',', '.') }}
                        </p>
                        <button class="btn btn-primary btn-sm btn-detail">
                            ➜ Lihat Detail
                        </button>
                    </div>

                </div>

            </a>

        </div>
        @endforeach

    </div>

    @else

    <div class="alert alert-warning text-center">
        <h5>⚠️ Tidak ada data obat</h5>
        <p>Silakan periksa file Excel atau coba pencarian dengan kata kunci lain.</p>
    </div>

    @endif

</div>

@endsection

@push('scripts')
<script>
    (function() {
        const statsUrl = '{{ url("/produk/stats") }}';
        const initialKategori = '{{ request("kategori") }}';

        const updateStats = function(stats) {
            const totalEl = document.getElementById('total-obat-count');
            const kerasEl = document.getElementById('obat-keras-count');
            const umumEl = document.getElementById('obat-umum-count');
            if (totalEl) {
                totalEl.textContent = stats.totalObat;
            }
            if (kerasEl) {
                kerasEl.textContent = stats.categoryCounts['Obat Keras'] || 0;
            }
            if (umumEl) {
                umumEl.textContent = stats.categoryCounts['Obat Umum'] || 0;
            }
        };

        const fetchStats = function() {
            fetch(statsUrl, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.ok ? response.json() : Promise.reject(response))
                .then(updateStats)
                .catch(error => console.error('Fetch stats failed', error));
        };

        window.addEventListener('load', function() {
            fetchStats();
            // auto-refresh removed to prevent automatic program updates
            // setInterval(fetchStats, 15000);
            // store current category in sessionStorage so detail page can return
            try {
                const sel = document.querySelector('form[action="/produk/filter"] select[name="kategori"]');
                if (sel) {
                    sel.addEventListener('change', function() {
                        if (this.value) sessionStorage.setItem('produk_kategori', this.value);
                        else sessionStorage.removeItem('produk_kategori');
                    });
                    if (initialKategori) {
                        sessionStorage.setItem('produk_kategori', initialKategori);
                    }
                }
            } catch (e) {
                // ignore
            }

            // intercept product list clicks to ensure from_kategori is appended
            try {
                const list = document.getElementById('product-list-row');
                if (list) {
                    list.addEventListener('click', function(ev) {
                        let target = ev.target;
                        while (target && target !== list && target.nodeName !== 'A') {
                            target = target.parentNode;
                        }
                        if (!target || target === list) return;
                        const href = target.getAttribute('href') || '';
                        if (!href.includes('/obat/')) return;

                        const params = new URLSearchParams(window.location.search);
                        let cat = params.get('kategori') || params.get('from_kategori');
                        const sel2 = document.querySelector('form[action="/produk/filter"] select[name="kategori"]');
                        if (!cat && sel2) cat = sel2.value;
                        if (!cat) cat = sessionStorage.getItem('produk_kategori');
                        if (!cat) return;

                        if (href.includes('?')) {
                            if (!href.includes('from_kategori=') && !href.includes('kategori=')) {
                                ev.preventDefault();
                                target.href = href + '&from_kategori=' + encodeURIComponent(cat);
                                window.location = target.href;
                            }
                        } else {
                            ev.preventDefault();
                            target.href = href + '?from_kategori=' + encodeURIComponent(cat);
                            window.location = target.href;
                        }
                    }, true);
                }
            } catch (e) {
                // ignore
            }
        });
    })();
</script>
@endpush

@push('scripts')
<script>
    (function() {
        const buttons = document.querySelectorAll('.btn-letter-user');
        if (!buttons || buttons.length === 0) return;
        buttons.forEach(btn => {
            btn.addEventListener('click', function() {
                const letter = this.dataset.letter || '';
                const input = document.querySelector('form[action="/produk/search"] input[name="q"]');
                if (input) input.value = letter;
                const form = document.querySelector('form[action="/produk/search"]');
                if (form) form.submit();
            });
        });
    })();
</script>
@endpush