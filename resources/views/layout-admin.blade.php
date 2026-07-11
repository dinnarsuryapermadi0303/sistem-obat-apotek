<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Apotek 24</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --bg: #eef2ff;
            --surface: #ffffff;
            --surface-soft: #f8fafc;
            --text: #0f172a;
            --muted: #64748b;
            --primary: #2563eb;
            --primary-soft: rgba(37, 99, 235, .12);
            --shadow: 0 24px 60px rgba(15, 23, 42, .1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: radial-gradient(circle at top, rgba(37, 99, 235, .14), transparent 32%),
                linear-gradient(180deg, #f8fbff 0%, #eef2ff 100%);
            color: var(--text);
        }

        .admin-navbar {
            background: linear-gradient(90deg, #0f172a, #1e40af);
            box-shadow: 0 18px 40px rgba(0, 0, 0, .12);
            padding: 18px 0;
        }

        .admin-navbar .navbar-brand {
            color: #fff;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: .02em;
        }

        .admin-navbar .navbar-brand small {
            display: block;
            font-size: 12px;
            opacity: .85;
            margin-top: 4px;
        }

        .admin-navbar .nav-link {
            color: rgba(255, 255, 255, .9) !important;
            padding: 10px 16px;
            margin: 0 4px;
            border-radius: 12px;
            transition: .25s ease;
            font-weight: 500;
        }

        .admin-navbar .nav-link:hover {
            background: rgba(255, 255, 255, .12);
            color: #fff !important;
        }

        .admin-navbar .nav-link.active {
            background: rgba(255, 255, 255, .2);
            color: #fff !important;
            font-weight: 600;
        }

        .admin-navbar .btn {
            border-radius: 12px;
            padding: 8px 16px;
        }

        .admin-content {
            max-width: 1180px;
            margin: 28px auto;
            padding: 0 16px;
        }

        .login-card {
            overflow: hidden;
            border-radius: 28px;
            background: rgba(255, 255, 255, .96);
            border: 1px solid rgba(15, 23, 42, .08);
        }

        .login-badge {
            width: 84px;
            height: 84px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            background: rgba(37, 99, 235, .12);
            color: #1d4ed8;
        }

        .info-card-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .info-card-list li {
            padding: 1rem 0;
            border-bottom: 1px solid rgba(15, 23, 42, .06);
        }

        .info-card-list li:last-child {
            border-bottom: none;
        }

        .info-card-list li::before {
            content: '\2022';
            color: var(--primary);
            display: inline-block;
            width: 1rem;
            margin-right: .5rem;
            font-size: 1.2rem;
            vertical-align: middle;
        }

        .card {
            border: none;
            border-radius: 22px;
            box-shadow: var(--shadow);
            transition: transform .25s ease, box-shadow .25s ease;
            background: var(--surface);
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 28px 80px rgba(15, 23, 42, .12);
        }

        .btn {
            border-radius: 14px;
        }

        .dashboard-hero {
            background: linear-gradient(135deg, #1d4ed8, #2563eb 42%, #60a5fa 100%);
            color: #fff;
            border-radius: 28px;
            overflow: hidden;
        }

        .dashboard-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top left, rgba(255, 255, 255, .22), transparent 35%);
            pointer-events: none;
        }

        .dashboard-hero .card-body {
            position: relative;
            z-index: 1;
        }

        .dashboard-stat-card {
            border-radius: 24px;
            min-height: 170px;
        }

        .dashboard-stat-card .icon-box {
            width: 56px;
            height: 56px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: rgba(37, 99, 235, .12);
        }

        .dashboard-info-card {
            border-radius: 24px;
            border: 1px solid rgba(15, 23, 42, .06);
            background: #fff;
        }

        .dashboard-chip {
            background: rgba(255, 255, 255, .14);
            color: #fff;
            border-radius: 999px;
            padding: .65rem 1rem;
            font-size: .9rem;
        }

        .dashboard-list li {
            position: relative;
            padding-left: 1.6rem;
            margin-bottom: .95rem;
        }

        .dashboard-list li::before {
            content: '';
            position: absolute;
            left: 0;
            top: .8rem;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--primary);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }

        .btn-secondary {
            border-radius: 14px;
        }

        .table {
            border-radius: 18px;
            overflow: hidden;
            background: var(--surface);
        }

        .table thead {
            background: var(--primary);
            color: #fff;
        }

        .form-control,
        .form-select {
            border-radius: 14px;
            border: 1px solid #dbe3ea;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .15);
        }

        .alert {
            border: none;
            border-radius: 16px;
        }

        footer {
            padding: 24px 0;
            text-align: center;
            color: var(--muted);
            margin-top: 36px;
        }

        .badge-soft-primary {
            background: var(--primary-soft);
            color: var(--primary);
        }
    </style>

</head>

<body>

    <nav class="navbar navbar-expand-lg admin-navbar shadow-sm">

        <div class="container">

            <a class="navbar-brand fw-bold"
                href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}">

                {{ session('nama_website','Admin Apotek 24') }}
                <small>Panel Administrasi</small>

            </a>

            <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarAdmin"
                aria-controls="navbarAdmin"
                aria-expanded="false"
                aria-label="Toggle navigation">

                <span class="navbar-toggler-icon"></span>

            </button>

            <div class="collapse navbar-collapse"
                id="navbarAdmin">

                <ul class="navbar-nav ms-auto align-items-center">

                    <li class="nav-item">

                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                            href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}">

                            Dashboard

                        </a>

                    </li>

                    <li class="nav-item">

                        <a class="nav-link {{ request()->routeIs('admin.produk*') ? 'active' : '' }}"
                            href="{{ Route::has('admin.produk') ? route('admin.produk') : '#' }}">

                            Produk

                        </a>

                    </li>

                    <li class="nav-item">

                        <a class="nav-link {{ request()->routeIs('admin.laporan*') ? 'active' : '' }}"
                            href="{{ Route::has('admin.laporan') ? route('admin.laporan') : '#' }}">

                            Laporan

                        </a>

                    </li>

                    <li class="nav-item">

                        <a class="nav-link {{ request()->routeIs('admin.validasi*') ? 'active' : '' }}"
                            href="{{ Route::has('admin.validasi') ? route('admin.validasi') : '#' }}">

                            Validasi

                        </a>

                    </li>

                    <li class="nav-item">

                        <a class="nav-link {{ request()->routeIs('admin.pengaturan') ? 'active' : '' }}"
                            href="{{ Route::has('admin.pengaturan') ? route('admin.pengaturan') : '#' }}">

                            Pengaturan

                        </a>

                    </li>

                    @if(session('admin_logged_in'))
                    <li class="nav-item ms-3">
                        <form action="{{ Route::has('admin.logout') ? route('admin.logout') : '#' }}" method="POST" class="mb-0">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">
                                <i class="bi bi-box-arrow-right me-1"></i>
                                Logout
                            </button>
                        </form>
                    </li>
                    @endif

                </ul>

            </div>

        </div>

    </nav>

    <div class="admin-content">

        @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show" role="alert">

            {{ session('success') }}

            <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"></button>

        </div>

        @endif

        @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show" role="alert">

            {{ session('error') }}

            <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"></button>

        </div>

        @endif

        @yield('content')

    </div>

    <footer>

        © 2026 Admin Apotek 24

    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>