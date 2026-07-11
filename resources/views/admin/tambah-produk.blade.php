@extends('layout-admin')

@section('content')

<div class="container py-5">

    <div class="card border-0 shadow rounded-4">

        <div class="card-body p-5">

            <h2 class="fw-bold mb-4">
                Tambah Produk
            </h2>

            {{-- ALERT --}}
            @if(session('success'))

            <div class="alert alert-success">

                {{ session('success') }}

            </div>

            @endif

            {{-- FORM --}}
            <form action="/admin/produk/store"
                method="POST">

                @csrf

                {{-- NAMA --}}
                <div class="mb-3">

                    <label class="form-label">
                        Nama Obat
                    </label>

                    <input
                        type="text"
                        name="nama"
                        class="form-control"
                        required>

                </div>

                {{-- KATEGORI --}}
                <div class="mb-3">

                    <label class="form-label">
                        Kategori
                    </label>

                    <select name="kategori" class="form-control" required>
                        <option value="Obat Umum">Obat Umum</option>
                        <option value="Obat Keras">Obat Keras</option>
                    </select>

                </div>

                {{-- HARGA --}}
                <div class="mb-3">

                    <label class="form-label">
                        Harga
                    </label>

                    <input
                        type="number"
                        name="harga"
                        class="form-control"
                        required>

                </div>

                {{-- DESKRIPSI --}}
                <div class="mb-4">

                    <label class="form-label">
                        Deskripsi
                    </label>

                    <textarea
                        name="deskripsi"
                        rows="4"
                        class="form-control"></textarea>

                </div>

                {{-- PENYAJIAN (mapped to `jenis`) --}}
                <div class="mb-3">

                    <label class="form-label">
                        Penyajian
                    </label>

                    <input type="text" name="jenis" class="form-control" placeholder="Contoh: Sirup / Tablet">

                </div>

                {{-- INDIKASI --}}
                <div class="mb-3">

                    <label class="form-label">
                        Indikasi
                    </label>

                    <textarea name="indikasi" rows="2" class="form-control"></textarea>

                </div>

                {{-- Komposisi removed; use Penyajian (`jenis`) instead --}}

                {{-- DOSIS --}}
                <div class="mb-3">

                    <label class="form-label">
                        Dosis
                    </label>

                    <input type="text" name="dosis" class="form-control">

                </div>

                {{-- EFEK SAMPING --}}
                <div class="mb-3">

                    <label class="form-label">
                        Efek Samping
                    </label>

                    <textarea name="efek_samping" rows="2" class="form-control"></textarea>

                </div>

                {{-- Kontraindikasi removed per UI request --}}

                {{-- BUTTON --}}
                <button
                    type="submit"
                    class="btn btn-primary">
                    Simpan Produk
                </button>

                <a href="/admin/produk"
                    class="btn btn-secondary">

                    Kembali

                </a>

            </form>

        </div>

    </div>

</div>

@endsection