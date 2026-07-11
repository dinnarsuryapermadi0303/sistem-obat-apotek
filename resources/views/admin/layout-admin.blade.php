<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Admin Apotek 24</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <style>
        body {

            background: #f4f7fb;

            font-family: 'Segoe UI', sans-serif;

            min-height: 100vh;

            /* reserve space for fixed navbar */
            padding-top: 72px;

        }

        /* ================= NAVBAR ================= */

        .navbar {

            background: linear-gradient(90deg, #0f172a, #1e40af);

            box-shadow: 0 10px 30px rgba(0, 0, 0, .12);

            padding: 12px 0;

        }

        .navbar-brand {

            font-size: 24px;

            font-weight: 700;

            color: #fff !important;

        }

        .navbar-nav .nav-link {

            color: rgba(255, 255, 255, .85) !important;

            padding: 10px 18px;

            margin: 0 5px;

            border-radius: 10px;

            transition: .3s;

        }

        .navbar-nav .nav-link:hover {

            background: rgba(255, 255, 255, .12);

            color: #fff !important;

        }

        .navbar-nav .nav-link.active {

            background: #2563eb;

            color: #fff !important;

            font-weight: 600;

        }

        /* ================= CONTENT ================= */

        main {

            min-height: 85vh;

            padding-top: 30px;

            padding-bottom: 30px;

        }

        /* ================= CARD ================= */

        .card {

            border: none;

            border-radius: 18px;

            box-shadow: 0 10px 25px rgba(0, 0, 0, .08);

            transition: .3s;

        }

        .card:hover {

            transform: translateY(-4px);

        }

        /* ================= BUTTON ================= */

        .btn {

            border-radius: 10px;

        }

        /* ================= TABLE ================= */

        .table {

            border-radius: 15px;

            overflow: hidden;

        }

        .table thead {

            background: #2563eb;

            color: #fff;

        }

        /* ================= FORM ================= */

        .form-control,

        .form-select {

            border-radius: 10px;

        }

        .form-control:focus,

        .form-select:focus {

            border-color: #2563eb;

            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .15);

        }

        /* ================= ALERT ================= */

        .alert {

            border: none;

            border-radius: 12px;

        }

        /* ================= FOOTER ================= */

        footer {

            background: #fff;

            border-top: 1px solid #e5e7eb;

            padding: 18px;

            text-align: center;

            color: #64748b;

            margin-top: 40px;

        }
    </style>

</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">

        <div class="container">

            <a class="navbar-brand"
                href="{{ url('/admin') }}">

                <i class="bi bi-capsule-pill me-2"></i>

                Admin Apotek 24

            </a>

            <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">

                <span class="navbar-toggler-icon"></span>

            </button>

            <div class="collapse navbar-collapse"
                id="navbarNav">

                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">

                        <a class="nav-link {{ request()->is('admin') ? 'active' : '' }}"
                            href="{{ url('/admin') }}">

                            <i class="bi bi-speedometer2 me-1"></i>

                            Dashboard

                        </a>

                    </li>

                    <li class="nav-item">

                        <a class="nav-link {{ request()->is('admin/produk*') ? 'active' : '' }}"
                            href="{{ url('/admin/produk') }}">

                            <i class="bi bi-box-seam me-1"></i>

                            Produk

                        </a>

                    </li>

                    <li class="nav-item">

                        <a class="nav-link {{ request()->is('admin/produk/tambah') ? 'active' : '' }}"
                            href="{{ url('/admin/produk/tambah') }}">

                            <i class="bi bi-plus-circle me-1"></i>

                            Tambah Produk

                        </a>

                    </li>

                    <li class="nav-item">

                        <a class="nav-link {{ request()->is('admin/laporan*') ? 'active' : '' }}"
                            href="{{ url('/admin/laporan') }}">

                            <i class="bi bi-file-earmark-text me-1"></i>

                            Laporan

                        </a>

                    </li>

                    <li class="nav-item">

                        <a class="nav-link {{ request()->is('admin/pengaturan*') ? 'active' : '' }}"
                            href="{{ url('/admin/pengaturan') }}">

                            <i class="bi bi-gear me-1"></i>

                            Pengaturan

                        </a>

                    </li>

                </ul>

            </div>

        </div>

    </nav>

    <main class="container-fluid">

        <div class="container">

            @if(session('success'))

            <div class="alert alert-success alert-dismissible fade show">

                {{ session('success') }}

                <button class="btn-close"
                    data-bs-dismiss="alert"></button>

            </div>

            @endif

            @if(session('error'))

            <div class="alert alert-danger alert-dismissible fade show">

                {{ session('error') }}

                <button class="btn-close"
                    data-bs-dismiss="alert"></button>

            </div>

            @endif

            @yield('content')

        </div>

    </main>

    <footer>

        <strong>Admin Apotek 24</strong><br>

        Sistem Rekomendasi Obat Berbasis Rule Based, TF-IDF & Cosine Similarity

    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>