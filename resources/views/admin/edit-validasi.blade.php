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

    <div class="card shadow border-0 rounded-4">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold text-primary">

                        <i class="bi bi-pencil-square"></i>

                        Edit Validasi

                    </h2>

                    <p class="text-muted mb-0">

                        Perbarui hasil validasi rekomendasi obat pengguna.

                    </p>

                </div>

                <a href="{{ route('admin.validasi') }}"

                    class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i>

                    Kembali

                </a>

            </div>

            <hr>

            <form

                action="{{ route('admin.validasi.update',$selected['id']) }}"

                method="POST">

                @csrf

                <div class="row">

                    <div class="col-md-6">

                        <h5 class="fw-bold mb-3">

                            Data Pengguna

                        </h5>

                        <table class="table">

                            <tr>

                                <th width="35%">

                                    Tanggal

                                </th>

                                <td>

                                    {{ $selected['tanggal'] ?? '-' }}

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Nama

                                </th>

                                <td>

                                    {{ $selected['nama'] ?? '-' }}

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Usia

                                </th>

                                <td>

                                    {{ $selected['usia'] ?? '-' }}

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Keluhan

                                </th>

                                <td>

                                    {{ $selected['keluhan'] ?? '-' }}

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Durasi

                                </th>

                                <td>

                                    {{ $selected['durasi'] ?? '-' }}

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Riwayat

                                </th>

                                <td>

                                    {{ $selected['riwayat'] ?? '-' }}

                                </td>

                            </tr>

                        </table>

                    </div>
                    <div class="col-md-6">

                        <h5 class="fw-bold mb-3">

                            Hasil Validasi

                        </h5>

                        <div class="mb-3">

                            <label class="form-label">

                                Obat Yang Disetujui

                            </label>

                            @foreach($recommendedProduk as $obat)

                            <div class="form-check">

                                <input

                                    class="form-check-input"

                                    type="checkbox"

                                    name="approved_meds[]"

                                    value="{{ $obat['nama'] }}"

                                    {{ in_array($obat['nama'], $selected['approved_meds'] ?? []) ? 'checked' : '' }}>

                                <label class="form-check-label">

                                    {{ $obat['nama'] }}

                                    <span class="badge bg-info ms-2">

                                        {{ number_format($obat['similarity_pct'] ?? 0,2) }}%

                                    </span>

                                </label>

                            </div>

                            @endforeach

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Similarity

                            </label>

                            <input

                                type="text"

                                class="form-control"

                                value="{{ number_format($selected['similarity'] ?? 0,2) }} %"

                                readonly>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Confidence

                            </label>

                            <input

                                type="text"

                                class="form-control"

                                value="{{ $selected['confidence'] ?? '-' }}"

                                readonly>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Status Validasi

                            </label>

                            <select

                                name="admin_status"

                                class="form-select"

                                required>

                                <option value="Menunggu Validasi"

                                    {{ ($selected['admin_status'] ?? '')=='Menunggu Validasi' ? 'selected' : '' }}>

                                    Menunggu Validasi

                                </option>

                                <option value="Disetujui Admin"

                                    {{ ($selected['admin_status'] ?? '')=='Disetujui Admin' ? 'selected' : '' }}>

                                    Disetujui Admin

                                </option>

                                <option value="Ditolak Admin"

                                    {{ ($selected['admin_status'] ?? '')=='Ditolak Admin' ? 'selected' : '' }}>

                                    Ditolak Admin

                                </option>

                            </select>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Catatan Admin

                            </label>

                            <textarea

                                name="admin_conditions"

                                rows="5"

                                class="form-control">{{ $selected['admin_conditions'] ?? '' }}</textarea>

                        </div>

                        <div class="form-check form-switch mb-4">

                            <input

                                class="form-check-input"

                                type="checkbox"

                                name="pdf_ready"

                                value="1"

                                {{ ($selected['pdf_ready'] ?? false) ? 'checked' : '' }}>

                            <label class="form-check-label">

                                PDF Siap Diunduh

                            </label>

                        </div>

                    </div>

                </div>
                <div class="d-flex justify-content-between mt-4">

                    <a

                        href="{{ route('admin.validasi') }}"

                        class="btn btn-secondary">

                        <i class="bi bi-arrow-left"></i>

                        Kembali

                    </a>

                    <div>

                        <button

                            type="reset"

                            class="btn btn-warning">

                            <i class="bi bi-arrow-clockwise"></i>

                            Reset

                        </button>

                        <button

                            type="submit"

                            class="btn btn-primary">

                            <i class="bi bi-check-circle"></i>

                            Simpan Perubahan

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

<style>
    .card {

        border-radius: 18px;

    }

    .table th {

        width: 35%;

        font-weight: 600;

        vertical-align: middle;

    }

    .table td {

        vertical-align: middle;

    }

    .form-control,

    .form-select {

        border-radius: 10px;

    }

    .form-check {

        margin-bottom: 8px;

    }

    .btn {

        border-radius: 10px;

    }

    .alert {

        border-radius: 12px;

    }

    textarea {

        resize: none;

    }
</style>

@endsection