@if(isset($produk) && count($produk) > 0)

<div id="produk-list" class="row">

    @foreach($produk as $item)
    @php
    $kategoriValue = trim($item['kategori'] ?? 'Tanpa Kategori');
    $isKeras = $kategoriValue === 'Obat Keras';
    $isUmum = $kategoriValue === 'Obat Umum';
    $logoBg = $isKeras ? 'bg-danger' : ($isUmum ? 'bg-success' : 'bg-primary');
    $logoIcon = $isKeras ? 'bi-shield-lock' : ($isUmum ? 'bi-heart-pulse' : 'bi-tags');
    $badgeClass = $isKeras ? 'bg-danger' : ($isUmum ? 'bg-success' : 'bg-primary');
    @endphp
    <div class="col-md-3 mb-4">

        @php
        $fromCat = request('kategori');
        $detailUrl = url('/obat/' . rawurlencode($item['nama']));
        if (!empty($fromCat)) {
        $detailUrl .= '?from_kategori=' . rawurlencode($fromCat);
        }
        @endphp
        <a href="{{ $detailUrl }}" style="text-decoration:none; color:inherit;">

            <div class="card text-center h-100" style="border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 10px; transition: transform 0.3s;">

                <div class="d-flex align-items-center justify-content-center {{ $logoBg }} text-white" style="height: 200px; border-radius: 10px 10px 0 0;">
                    <div class="text-center">
                        <i class="bi {{ $logoIcon }} fs-1"></i>
                        <div class="mt-3 small fw-semibold">{{ $kategoriValue }}</div>
                    </div>
                </div>

                <div class="card-body">
                    <h5 style="font-weight: bold;">{{ $item['nama'] }}</h5>
                    <p class="text-muted mb-1">{{ $kategoriValue }}</p>
                    <span class="badge {{ $badgeClass }} mb-2">{{ $kategoriValue }}</span>
                    <small class="text-muted" style="display: block; margin: 8px 0;">
                        📝 {{ Str::limit($item['deskripsi'] ?? '-', 50) }}
                    </small>
                    @php
                    $hargaValue = $item['harga'] ?? 0;
                    if (!is_int($hargaValue) && !is_float($hargaValue)) {
                    $hargaValue = preg_replace('/[^0-9\.]/', '', (string)$hargaValue);
                    $hargaValue = $hargaValue === '' ? 0 : (float)$hargaValue;
                    }
                    @endphp
                    <p class="text-success fw-bold" style="margin-top: 10px;">
                        Rp {{ number_format($hargaValue, 0, ',', '.') }}
                    </p>

                    <div class="d-grid gap-2">
                        <a href="/admin/produk/{{ $item['id'] }}" class="btn btn-primary btn-sm">➜ Lihat Detail</a>
                        <div class="d-flex justify-content-between">
                            <a href="/admin/produk/{{ $item['id'] }}/edit" class="btn btn-warning btn-sm">Edit</a>
                            <form action="/admin/produk/{{ $item['id'] }}/hapus" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

        </a>

    </div>
    @endforeach

</div>

@else

<div class="alert alert-warning text-center">
    <h5>⚠️ Data produk kosong</h5>
    <p>Silakan tambahkan produk baru.</p>
</div>

@endif

@push('scripts')
<script>
    (function() {
        try {
            const list = document.getElementById('produk-list');
            if (!list) return;
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
                const sel = document.querySelector('select[name="kategori"]');
                if (!cat && sel) cat = sel.value;
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
        } catch (e) {
            // ignore
        }
    })();
</script>
@endpush