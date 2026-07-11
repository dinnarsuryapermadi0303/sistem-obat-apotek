@extends('layout-admin')

@section('content')

<div class="container py-4">

    <div class="row mb-4">

        <div class="col-md-12">

            <h2 class="fw-bold text-primary">
                Pengaturan Admin
            </h2>

            <p class="text-muted">
                Kelola konfigurasi website Apotek 24.
            </p>

        </div>

    </div>

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button class="btn-close"
                    data-bs-dismiss="alert"></button>

        </div>

    @endif

    <div class="row align-items-start">

        <div class="col-lg-7">

            <div class="card border-0 shadow rounded-4 h-100">

                <div class="card-body p-5">

                    <form action="{{ route('admin.pengaturan.update') }}"
                          method="POST">

                        @csrf

                        <div class="mb-3">

                            <label class="form-label fw-semibold">

                                Nama Website

                            </label>

                            <input
                                type="text"
                                name="nama_website"
                                class="form-control"
                                value="{{ session('nama_website','Apotek 24') }}">

                        </div>

                        <div class="mb-3">

                            <label class="form-label fw-semibold">

                                Email Admin

                            </label>

                            <input
                                type="email"
                                name="email_admin"
                                class="form-control"
                                value="{{ session('admin_settings.email', session('admin_email','admin@apotek24.local')) }}">

                        </div>

                        <div class="mb-3">

                            <label class="form-label fw-semibold">

                                Password Baru

                            </label>

                            <input
                                type="password"
                                name="password_admin"
                                class="form-control"
                                placeholder="Kosongkan jika tidak diubah">

                        </div>

                        <div class="mb-3">

                            <label class="form-label fw-semibold">

                                Konfirmasi Password

                            </label>

                            <input
                                type="password"
                                name="password_confirmation"
                                class="form-control">

                        </div>

                        <div class="mb-4">

                            <label class="form-label fw-semibold">

                                Status Website

                            </label>

                            <select
                                name="status_website"
                                class="form-select">

                                <option value="Aktif"
                                    {{ session('status_website','Aktif')=='Aktif'?'selected':'' }}>
                                    Aktif
                                </option>

                                <option value="Tidak Aktif"
                                    {{ session('status_website')=='Tidak Aktif'?'selected':'' }}>
                                    Tidak Aktif
                                </option>

                            </select>

                        </div>

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary">

                                Simpan Pengaturan

                            </button>

                            <button
                                type="reset"
                                class="btn btn-secondary">

                                Reset

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

        <div class="col-lg-5">

            <div class="card border-0 shadow rounded-4 h-100">

                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3">

                        Informasi

                    </h5>

                    <ul class="info-card-list">

                        <li class="list-group-item">

                            Nama website akan tampil di halaman utama.

                        </li>

                        <li class="list-group-item">

                            Email digunakan sebagai akun administrator.

                        </li>

                        <li class="list-group-item">

                            Password hanya berubah jika diisi.

                        </li>

                        <li class="list-group-item">

                            Status tidak aktif akan menonaktifkan akses pengguna.

                        </li>
                        <li class="list-group-item">

                            Login admin akan menggunakan email dan password terbarui.

                        </li>

                    </ul>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection