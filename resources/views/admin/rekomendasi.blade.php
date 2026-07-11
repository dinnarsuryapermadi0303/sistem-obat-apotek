@extends('layout-admin')

@section('content')

<div class="container py-5">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold">
                Data Produk
            </h2>

            <p class="text-muted">
                Daftar semua produk obat.
            </p>

        </div>

        <a href="/admin/produk/tambah"
            class="btn btn-primary">

            + Tambah Produk

        </a>

    </div>

    {{-- ALERT --}}
    @if(session('success'))

    <div class="alert alert-success">

        {{ session('success') }}

    </div>

    @endif

    {{-- TABLE --}}
    <div class="card border-0 shadow rounded-4">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered align-middle">

                    <thead class="table-dark">

                        <tr>

                            <th width="70">
                                No
                            </th>

                            <th>
                                Nama Obat
                            </th>

                            <th>
                                Kategori
                            </th>

                            <th>
                                Harga
                            </th>

                            <th width="200">
                                Aksi
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($produk as $index => $item)

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>
                                {{ $item['nama'] ?? '-' }}
                            </td>

                            <td>
                                {{ $item['kategori'] ?? '-' }}
                            </td>

                            <td>
                                Rp
                                {{ number_format($item['harga'] ?? 0, 0, ',', '.') }}
                            </td>

                            <td>

                                {{-- EDIT --}}
                                <a href="/admin/produk/{{ $item['id'] }}/edit"
                                    class="btn btn-warning btn-sm">

                                    Edit

                                </a>

                                {{-- HAPUS --}}
                                <form action="/admin/produk/{{ $item['id'] }}/hapus"
                                    method="POST"
                                    class="d-inline">

                                    @csrf

                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Hapus produk ini?')">
                                        Hapus
                                    </button>

                                </form>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="5"
                                class="text-center text-muted">

                                Data produk kosong

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection