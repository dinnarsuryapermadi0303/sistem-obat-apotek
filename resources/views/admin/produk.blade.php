@extends('layout-admin')

@section('content')
{{-- HEADER: Statistik, search, filter (sama seperti user) --}}
<div class="text-center mb-4">
    <h2>Daftar Produk Obat</h2>
    <p class="text-muted">Produk obat yang tersedia di Apotek Dinar</p>
</div>

<div class="glass-box mb-4">
    <div class="row gy-3">
        <div class="col-md-4">
            <div class="p-3 rounded-3" style="background: #f5f7ff;">
                <p class="mb-1 text-muted" style="font-size: 0.95rem;">Total Obat</p>
                <h4 class="mb-0">{{ $totalProduk ?? (count($produk) ?? 0) }}</h4>
            </div>
        </div>
        @if(isset($categoryCounts) && count($categoryCounts) > 0)
        @foreach($categoryCounts as $categoryName => $count)
        <div class="col-md-4">
            <div class="p-3 rounded-3" style="background: #fff7f6;">
                <p class="mb-1 text-muted" style="font-size: 0.95rem;">{{ $categoryName }}</p>
                <h4 class="mb-0">{{ $count }}</h4>
            </div>
        </div>
        @endforeach
        @endif
    </div>
    <div class="row mt-3">
        <div class="col-md-8 mb-2">
            <form action="/admin/produk" method="GET">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Cari obat..." value="{{ $queryText ?? request('q') }}">
                    <button class="btn btn-primary" type="submit">🔍 Cari</button>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <form action="/admin/produk" method="GET">
                <select name="kategori" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @if(isset($categories) && count($categories) > 0)
                    @foreach($categories as $category)
                    <option value="{{ $category }}" {{ (isset($filterKategori) && $filterKategori == $category) || request('kategori') == $category ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                    @endif
                </select>
            </form>
        </div>
    </div>
    <div class="d-flex justify-content-end mt-3 mb-3">
        <a href="/admin/produk/tambah" class="btn btn-success">＋ Tambah Obat</a>
    </div>

    <div class="mb-3">
        <form action="/admin/produk/import" method="POST" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
            @csrf
            <label class="form-label mb-0">Import Excel:</label>
            <input type="file" name="file" accept=".xlsx,.xls" class="form-control form-control-sm" style="max-width:360px;">
            <button class="btn btn-primary btn-sm">Upload & Import</button>
        </form>
    </div>
</div>

{{-- LIST PRODUK (partial untuk AJAX) --}}
<div id="produk-list-container">
    @include('admin._produk_list', ['produk' => $produk ?? []])
</div>

{{-- Modal Tambah Obat --}}
<div class="modal fade" id="modalTambahObat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Produk Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formTambahObat">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama</label>
                            <input name="nama" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori</label>
                            <select name="kategori" class="form-control" required>
                                <option value="Obat Umum">Obat Umum</option>
                                <option value="Obat Keras">Obat Keras</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga</label>
                            <input name="harga" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Penyajian</label>
                            <input name="jenis" class="form-control" placeholder="Contoh: Sirup / Tablet">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Indikasi</label>
                            <textarea name="indikasi" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Dosis</label>
                            <input name="dosis" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Efek Samping</label>
                            <input name="efek_samping" class="form-control">
                        </div>
                        {{-- Komposisi & Kontraindikasi removed; use Penyajian (`jenis`) instead --}}
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button id="btnSimpanObat" type="button" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="mb-3">
    <div class="d-flex flex-wrap gap-2">
        @foreach(range('A','Z') as $letter)
        <button type="button" class="btn btn-outline-secondary btn-sm btn-letter" data-letter="{{ $letter }}">{{ $letter }}</button>
        @endforeach
    </div>
</div>

<script>
    (function() {
        // Helper to refresh product list via AJAX
        function refreshList(q, kategori) {
            const qenc = encodeURIComponent(q || '');
            const kenc = encodeURIComponent(kategori || '');
            fetch(`/admin/produk/list?q=${qenc}&kategori=${kenc}`, {
                    credentials: 'same-origin'
                })
                .then(r => r.text())
                .then(html => {
                    const container = document.getElementById('produk-list-container');
                    if (container) container.innerHTML = html;
                }).catch(err => console.error(err));
        }

        // Form submit (search) via AJAX when clicking the search button or pressing Enter
        const searchForm = document.querySelector('form[action="/admin/produk"]');
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const input = searchForm.querySelector('input[name="q"]');
                const select = document.querySelector('select[name="kategori"]');
                const q = input ? input.value.trim() : '';
                const kategori = select ? select.value : '';
                refreshList(q, kategori);
            });
        }

        // Alphabet quick filter
        document.querySelectorAll('.btn-letter').forEach(btn => {
            btn.addEventListener('click', function() {
                const letter = this.dataset.letter || '';
                const select = document.querySelector('select[name="kategori"]');
                const kategori = select ? select.value : '';
                // set input value for UX
                const input = document.querySelector('input[name="q"]');
                if (input) input.value = letter;
                refreshList(letter, kategori);
            });
        });

        // Modal add product trigger
        const btnTambah = document.querySelector('a[href="/admin/produk/tambah"]');
        if (btnTambah) {
            btnTambah.addEventListener('click', function(e) {
                e.preventDefault();
                const modal = new bootstrap.Modal(document.getElementById('modalTambahObat'));
                modal.show();
            });
        }

        // Submit tambah produk via AJAX
        const btnSimpan = document.getElementById('btnSimpanObat');
        if (btnSimpan) {
            btnSimpan.addEventListener('click', function() {
                const form = document.getElementById('formTambahObat');
                if (!form) return;
                const data = new FormData(form);
                fetch('/admin/produk/store', {
                    method: 'POST',
                    body: data,
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]') ? document.querySelector('input[name="_token"]').value : ''
                    }
                }).then(async res => {
                    if (res.status === 201 || res.ok) {
                        const modalEl = document.getElementById('modalTambahObat');
                        const modalObj = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modalObj.hide();

                        // refresh list preserving current filters
                        const input = document.querySelector('input[name="q"]');
                        const select = document.querySelector('select[name="kategori"]');
                        const q = input ? input.value.trim() : '';
                        const kategori = select ? select.value : '';
                        refreshList(q, kategori);
                    } else if (res.status === 422) {
                        const body = await res.json();
                        alert('Validasi gagal: ' + JSON.stringify(body.errors || body));
                    } else {
                        const text = await res.text();
                        alert('Gagal menyimpan: ' + text);
                    }
                }).catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan saat menyimpan produk.');
                });
            });
        }
    })();
</script>
@endsection