@extends('layout-admin')

@section('content')

@php
use Illuminate\Support\Str;
@endphp

<div class="container py-5">
    @if(session('success'))

    <div class="alert alert-success">

        {{ session('success') }}

    </div>

    @endif

    @if(session('error'))

    <div class="alert alert-danger">

        {{ session('error') }}

    </div>

    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Konfirmasi Validasi</h2>
            <p class="text-muted">Konfirmasi permintaan validasi berikut, pilih obat yang direkomendasikan dan isi ketentuan.</p>
        </div>
    </div>

    <div class="card p-4 mb-4">
        <h5>Data Pengguna</h5>
        <p><strong>Nama :</strong> {{ $selected['nama'] ?? '-' }}</p>

        <p><strong>Usia :</strong> {{ $selected['usia'] ?? '-' }}</p>

        <p><strong>Keluhan :</strong> {{ $selected['keluhan'] ?? '-' }}</p>

        <p><strong>Durasi :</strong> {{ $selected['durasi'] ?? '-' }}</p>

        <p><strong>Riwayat :</strong> {{ $selected['riwayat'] ?? '-' }}</p>
    </div>

    <form action="{{ route('admin.approve', $selected['id']) }}" method="POST">
        @csrf

        <div class="card p-4 mb-4">
            <h5>Pilih Obat Yang Direkomendasikan</h5>

            <div class="row">
                @if($recommendedProduk->isNotEmpty())
                @foreach($recommendedProduk as $p)
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="approved[]" value="{{ $p['nama'] ?? '' }}" id="med-{{ md5($p['nama'] ?? '') }}"
                            {{ in_array($p['nama'], $selected['approved_meds'] ?? [], true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="med-{{ md5($p['nama'] ?? '') }}">
                            {{ $p['nama'] }}
                            <span class="badge bg-info ms-2">{{ number_format($p['similarity_pct'] ?? 0,2) }}%</span>
                        </label>
                        <div class="text-muted small mt-1">
                            Kategori: {{ $p['kategori'] ?? '-' }}
                        </div>
                        <div class="text-muted small">
                            Dosis: {{ $p['dosis'] ?? '-' }}
                        </div>
                        @if(!empty($p['indikasi']))
                        <div class="text-muted small">
                            Indikasi: {{ Str::limit($p['indikasi'], 100) }}
                        </div>
                        @endif
                        @if(!empty($p['deskripsi']))
                        <div class="text-muted small">
                            Deskripsi: {{ Str::limit($p['deskripsi'], 100) }}
                        </div>
                        @endif
                        @if(!empty($p['efek_samping']))
                        <div class="text-muted small">
                            Efek Samping: {{ Str::limit($p['efek_samping'], 100) }}
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
                @else
                <div class="col-12">
                    <div class="alert alert-warning">Tidak ada obat yang cocok dengan keluhan ini.</div>
                </div>
                @endif
            </div>
        </div>

        <div class="card p-4 mb-4">
            <h5>Ketentuan Admin</h5>
            <textarea name="conditions" class="form-control mb-3" rows="5" required placeholder="Isi ketentuan, aturan pakai, catatan resep, dsb.">{{ $selected['admin_conditions'] ?? '' }}</textarea>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">

                    <option value="Disetujui"
                        {{ ($selected['status'] ?? '') === 'Disetujui' ? 'selected' : '' }}>
                        Disetujui
                    </option>

                    <option value="Ditolak"
                        {{ ($selected['status'] ?? '') === 'Ditolak' ? 'selected' : '' }}>
                        Ditolak
                    </option>

                </select>

                @error('status')
                <div class="text-danger mt-2">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Simpan Konfirmasi
                </button>
                <a href="{{ route('admin.validasi') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </div>

    </form>

</div>

@endsection