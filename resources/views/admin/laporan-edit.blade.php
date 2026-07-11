@extends('layout-admin')

@section('content')

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Edit Laporan User</h2>
            <p class="text-muted">Perbarui detail laporan rekomendasi jika diperlukan.</p>
        </div>
        <a href="{{ route('admin.laporan') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="card border-0 shadow rounded-4 p-4">
        <form action="{{ route('admin.laporan.update', $index) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama', $selected['nama'] ?? '') }}" required>
            </div>

            <div class="mb-3 position-relative">
                <label class="form-label">Keluhan</label>
                @php
                $keluhanOptions = $keluhanOptions ?? collect([]);
                $currentKeluhan = old('keluhan', $selected['keluhan'] ?? '');
                // fallback defaults jika DB kosong
                $uiKeluhanOptions = $keluhanOptions->filter()->values();
                if ($uiKeluhanOptions->isEmpty()) {
                $uiKeluhanOptions = collect([
                'Batuk', 'Pilek', 'Demam', 'Influenza', 'Flu', 'Gejala', 'Reda', 'Bantu', 'Kurang',
                'Bersin', 'Kepala', 'Hidung', 'Sumbat', 'Dahak', 'Tenggorokan', 'Saluran', 'Napas',
                'Encer', 'Keluar', 'Mudah', 'Ekspektoran', 'Atasi', 'Alergi', 'Gatal', 'Mata',
                'Mukolitik', 'Bronkitis', 'Ringan', 'Sakit Kepala', 'Mual', 'Diare', 'Sembelit',
                'Nyeri Perut', 'Masalah Tidur'
                ]);
                }
                @endphp
                <div class="combo-container" style="position:relative;">
                    {{-- Hidden input holds the actual keluhan value(s) submitted to server. We join multiple picks with '|' --}}
                    <input type="hidden" id="keluhan-edit-hidden" name="keluhan" value="{{ old('keluhan', $selected['keluhan'] ?? '') }}">
                    <div id="keluhan-selected-list" class="mb-2 d-flex flex-wrap gap-2"></div>

                    <div class="d-flex gap-2 align-items-center">
                        <input id="keluhan-edit-input" class="form-control combo-input" autocomplete="off" value="" placeholder="Pilih atau ketik keluhan... (maks 6 pilihan)">
                        <button type="button" id="keluhan-add-option" class="btn btn-outline-secondary btn-sm">Tambah opsi</button>
                        <span id="keluhan-edit-toggle" title="Tampilkan daftar" style="cursor:pointer;z-index:1060;color:#6b7280">
                            <i class="bi bi-caret-down-fill"></i>
                        </span>
                    </div>
                    <div id="keluhan-edit-list" class="combo-list d-none" style="position:absolute;left:0;right:0;background:#fff;border:1px solid rgba(0,0,0,.08);border-radius:.75rem;max-height:220px;overflow:auto;z-index:1050;">
                        @foreach($uiKeluhanOptions as $option)
                        <div class="combo-item px-3 py-2" data-value="{{ $option }}">{{ $option }}</div>
                        @endforeach
                        @if($currentKeluhan && !$uiKeluhanOptions->contains($currentKeluhan))
                        <div class="combo-item px-3 py-2" data-value="{{ $currentKeluhan }}">{{ $currentKeluhan }}</div>
                        @endif
                    </div>
                </div>
                <style>
                    /* Keluhan chips */
                    #keluhan-selected-list {
                        gap: .5rem;
                    }

                    .keluhan-chip {
                        display: inline-flex;
                        align-items: center;
                        background: #f1f5f9;
                        color: #0f172a;
                        padding: .35rem .6rem;
                        border-radius: 999px;
                        border: 1px solid rgba(15, 23, 42, 0.08);
                        font-size: .9rem;
                    }

                    .keluhan-remove {
                        background: transparent;
                        border: 0;
                        color: rgba(15, 23, 42, 0.7);
                        margin-left: .35rem;
                        padding: 0 .15rem;
                        font-weight: 700;
                        cursor: pointer;
                    }

                    .combo-container {
                        position: relative;
                    }

                    .combo-list {
                        position: absolute;
                        top: calc(100% + .5rem);
                        left: 0;
                        right: 0;
                        background: #fff;
                        border: 1px solid rgba(148, 163, 184, 0.2);
                        border-radius: .9rem;
                        padding: .75rem;
                        max-height: 220px;
                        overflow-y: auto;
                        display: flex;
                        flex-wrap: wrap;
                        gap: .5rem;
                        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
                        z-index: 1050;
                    }

                    .combo-list.d-none {
                        display: none;
                    }

                    .combo-item {
                        padding: .45rem .9rem;
                        border-radius: 999px;
                        background: #f8fafc;
                        border: 1px solid rgba(148, 163, 184, 0.2);
                        color: #0f172a;
                        cursor: pointer;
                        white-space: nowrap;
                        font-size: .92rem;
                        transition: background .15s ease;
                    }

                    .combo-item:hover,
                    .combo-item.active {
                        background: #e0e7ff;
                    }

                    .combo-input {
                        min-width: 0;
                        flex: 1;
                    }

                    #keluhan-add-option {
                        white-space: nowrap;
                    }
                </style>
                <small id="keluhan-edit-feedback" class="form-text text-danger"></small>
                <script id="keluhan-edit-options-json" type="application/json">
                    {
                        !!json_encode($uiKeluhanOptions - > toArray(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!
                    }
                </script>
            </div>

            <div class="mb-3">
                <label class="form-label">Durasi</label>
                <input type="text" name="durasi" class="form-control" value="{{ old('durasi', $selected['durasi'] ?? '') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Riwayat</label>
                <textarea name="riwayat" class="form-control" rows="4">{{ old('riwayat', $selected['riwayat'] ?? '') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Jumlah Rekomendasi</label>
                <input type="number" name="hasil" class="form-control" value="{{ old('hasil', $selected['hasil'] ?? 0) }}" min="0" required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('admin.laporan') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', async function() {
        const keluhanInput = document.getElementById('keluhan-edit-input');
        const keluhanHidden = document.getElementById('keluhan-edit-hidden');
        const keluhanSelectedList = document.getElementById('keluhan-selected-list');
        const keluhanFeedback = document.getElementById('keluhan-edit-feedback');
        const submitBtn = document.querySelector('form button[type="submit"]');
        const keluhanEditList = document.getElementById('keluhan-edit-list');
        const editToggle = document.getElementById('keluhan-edit-toggle');

        const MAX_SELECTIONS = 6;

        function parseHiddenValue(val) {
            if (!val) return [];
            // support pipe-separated or comma-separated (legacy)
            const parts = val.split(/\||,/).map(s => s.trim()).filter(Boolean);
            return parts;
        }

        let selected = parseHiddenValue(keluhanHidden.value || '');

        function renderSelected() {
            keluhanSelectedList.innerHTML = '';
            selected.forEach((v, idx) => {
                const span = document.createElement('span');
                span.className = 'keluhan-chip';
                span.textContent = v;

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'keluhan-remove';
                btn.innerHTML = '&times;';
                btn.addEventListener('click', function() {
                    selected.splice(idx, 1);
                    updateHidden();
                    renderSelected();
                });

                span.appendChild(btn);
                keluhanSelectedList.appendChild(span);
            });
            // show count
            keluhanFeedback.textContent = selected.length ? `${selected.length} pilihan (maks ${MAX_SELECTIONS})` : '';
            keluhanFeedback.className = selected.length ? 'form-text text-muted' : 'form-text';
        }

        function updateHidden() {
            keluhanHidden.value = selected.join('|');
            // update submit state
            submitBtn.disabled = false;
        }

        function addSelection(value) {
            const values = String(value || '').split(',').map(v => v.trim()).filter(Boolean);
            for (const item of values) {
                if (selected.length >= MAX_SELECTIONS) {
                    keluhanFeedback.textContent = `⚠️ Maksimum ${MAX_SELECTIONS} pilihan.`;
                    keluhanFeedback.className = 'form-text text-danger fw-semibold';
                    return;
                }
                selected.push(item);
            }
            updateHidden();
            renderSelected();
        }

        function clearInput() {
            keluhanInput.value = '';
        }

        // reuse existing helpers for list population/filtering
        function getEditItems() {
            return keluhanEditList ? Array.from(keluhanEditList.querySelectorAll('.combo-item')) : [];
        }

        function showEditList() {
            if (keluhanEditList) {
                keluhanEditList.classList.remove('d-none');
                keluhanEditList.style.display = '';
            }
        }

        function hideEditList() {
            if (keluhanEditList) {
                keluhanEditList.classList.add('d-none');
                keluhanEditList.style.display = 'none';
            }
            keluhanEditIndex = -1;
            getEditItems().forEach(i => i.classList.remove('active'));
        }

        function filterEditList(q) {
            const ql = (q || '').toLowerCase().trim();
            getEditItems().forEach(item => {
                const txt = item.textContent.toLowerCase();
                item.style.display = txt.includes(ql) ? '' : 'none';
            });
        }

        async function populateEditList(items) {
            if (!keluhanEditList) return;
            keluhanEditList.innerHTML = '';
            if (!Array.isArray(items) || items.length === 0) return;
            items.forEach(v => {
                const div = document.createElement('div');
                div.className = 'combo-item px-3 py-2';
                div.dataset.value = v;
                div.textContent = v;
                keluhanEditList.appendChild(div);
            });
        }

        async function fetchKeluhanEdit(q) {
            try {
                const url = '/api/keluhan?q=' + encodeURIComponent(q || '');
                const res = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const json = await res.json();
                return json.data || [];
            } catch (e) {
                console.error('fetchKeluhanEdit error', e);
                return [];
            }
        }

        let keluhanEditServerOptions = [];
        try {
            const jsonSource = document.getElementById('keluhan-edit-options-json');
            keluhanEditServerOptions = jsonSource ? JSON.parse(jsonSource.textContent || '[]') : [];
        } catch (e) {
            console.warn('initial keluhan-edit parse error', e);
            keluhanEditServerOptions = [];
        }

        await populateEditList(keluhanEditServerOptions);

        // pre-render any existing selections
        renderSelected();

        keluhanInput.addEventListener('focus', async function() {
            const items = await fetchKeluhanEdit(this.value);
            await populateEditList(items.length ? items : keluhanEditServerOptions);
            filterEditList(this.value);
            showEditList();
        });

        let keluhanEditIndex = -1;

        keluhanInput.addEventListener('input', function() {
            filterEditList(this.value);
            if (keluhanEditList && keluhanEditList.classList.contains('d-none') === false) showEditList();
        });

        keluhanInput.addEventListener('keydown', function(e) {
            const items = getEditItems();
            const visible = items.filter(i => i.style.display !== 'none');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                keluhanEditIndex = Math.min(keluhanEditIndex + 1, visible.length - 1);
                items.forEach(i => i.classList.remove('active'));
                if (visible[keluhanEditIndex]) visible[keluhanEditIndex].classList.add('active');
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                keluhanEditIndex = Math.max(keluhanEditIndex - 1, 0);
                items.forEach(i => i.classList.remove('active'));
                if (visible[keluhanEditIndex]) visible[keluhanEditIndex].classList.add('active');
            } else if (e.key === ',' || e.key === 'Enter') {
                e.preventDefault();
                if (keluhanEditIndex >= 0 && visible[keluhanEditIndex]) {
                    addSelection(visible[keluhanEditIndex].dataset.value);
                } else if (keluhanInput.value && keluhanInput.value.trim()) {
                    addSelection(keluhanInput.value.trim());
                }
                clearInput();
                filterEditList('');
                showEditList();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                if (keluhanEditIndex >= 0 && visible[keluhanEditIndex]) {
                    addSelection(visible[keluhanEditIndex].dataset.value);
                } else if (keluhanInput.value && keluhanInput.value.trim()) {
                    addSelection(keluhanInput.value.trim());
                }
                clearInput();
                // keep list open to allow multiple selections without reopening
                filterEditList('');
                showEditList();
            } else if (e.key === 'Escape') {
                hideEditList();
            }
        });

        // Delegate clicks to container so dynamically added/changed items work
        if (keluhanEditList) {
            const addOptionBtn = document.getElementById('keluhan-add-option');
            if (addOptionBtn) {
                addOptionBtn.addEventListener('click', function(e) {
                    const val = (keluhanInput.value || '').trim();
                    if (!val) return;
                    const exists = getEditItems().some(i => i.dataset.value === val || i.textContent === val);
                    if (!exists) {
                        const div = document.createElement('div');
                        div.className = 'combo-item px-3 py-2';
                        div.dataset.value = val;
                        div.textContent = val;
                        keluhanEditList.insertBefore(div, keluhanEditList.firstChild);
                    }
                    addSelection(val);
                    clearInput();
                    filterEditList('');
                    showEditList();
                    keluhanInput.focus();
                });
            }

            keluhanEditList.addEventListener('click', function(e) {
                const item = e.target.closest('.combo-item');
                if (!item) return;
                addSelection(item.dataset.value || item.textContent);
                clearInput();
                // keep list open so user can pick additional items quickly
                filterEditList('');
                showEditList();
                keluhanInput.focus();
            });

            if (editToggle) {
                editToggle.addEventListener('click', async function(e) {
                    e.stopPropagation();
                    if (keluhanEditList.classList.contains('d-none')) {
                        const items = await fetchKeluhanEdit(keluhanInput.value);
                        await populateEditList(items);
                        showEditList();
                    } else {
                        hideEditList();
                    }
                    keluhanInput.focus();
                });
            }
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.combo-container')) hideEditList();
        });

    });
</script>

@endsection