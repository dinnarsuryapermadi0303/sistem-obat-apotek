<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Apotek 24</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {

            --primary: #2563eb;
            --secondary: #10b981;
            --orange: #ff7a00;
            --dark: #0f172a;
            --light: #f8fafc;
            --text: #334155;

        }

        * {

            margin: 0;
            padding: 0;
            box-sizing: border-box;

        }

        /* Limit transitions to non-layout properties to avoid layout shifts */
        a,
        .btn,
        .alert,
        .badge,
        .nav-link {
            transition: color .18s ease, background-color .18s ease, opacity .18s ease;
        }

        html {

            scroll-behavior: smooth;

        }

        body {

            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
            color: #334155;

        }

        /*================ NAVBAR =================*/

        .navbar-goa {

            position: fixed;

            top: 0;

            left: 50%;

            transform: translateX(-50%);

            width: 92%;

            max-width: 1200px;

            padding: 12px 25px;

            border-radius: 60px;

            background: rgba(255, 255, 255, .92);

            backdrop-filter: blur(20px);

            box-shadow:

                0 15px 35px rgba(0, 0, 0, .08);

            z-index: 999;

        }

        .navbar-brand {

            font-size: 30px;

            font-weight: 700;

        }

        .navbar-collapse {

            display: flex !important;

            justify-content: space-between;

            align-items: center;

            flex-wrap: wrap;

            gap: 12px;

        }

        .navbar-nav {

            display: flex !important;

            gap: 12px;

            align-items: center;

            flex-wrap: wrap;

        }

        .nav-link {

            font-weight: 500;

            padding: 10px 18px;

            border-radius: 15px;

            color: #475569 !important;

        }

        .nav-link:hover {

            background: #eef4ff;

            color: var(--primary) !important;

            transform: translateY(-2px);

        }

        .nav-link.active {

            background: var(--primary);

            color: white !important;

        }

        /*================ CONTENT =================*/

        .main-content {

            margin-top: 0;

            min-height: 85vh;

        }

        /*================ HERO =================*/

        .hero-goa {

            min-height: 100vh;

            display: flex;

            align-items: center;

            position: relative;

            overflow: hidden;

            background:

                linear-gradient(120deg, rgba(15, 23, 42, .82), rgba(37, 99, 235, .65)),

                url('/images/apotek.jpg');

            background-size: cover;

            background-position: center;

        }

        .hero-goa::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(255, 255, 255, .18), transparent 35%);
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            padding-top: 2rem;
            padding-bottom: 3rem;
        }

        .hero-panel {
            background: rgba(255, 255, 255, .92);
            border-radius: 28px;
            padding: 2rem;
            box-shadow: 0 20px 45px rgba(15, 23, 42, .2);
            backdrop-filter: blur(10px);
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .6rem .95rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, .14);
            color: white;
            font-size: .88rem;
            font-weight: 600;
            margin-bottom: 1rem;
            border: 1px solid rgba(255, 255, 255, .18);
        }

        .hero-stat-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            margin-top: 1.2rem;
        }

        .hero-stat {
            background: #f8fbff;
            border-radius: 16px;
            padding: .9rem;
            border: 1px solid #e2e8f0;
        }

        .hero-stat strong {
            display: block;
            color: var(--primary);
            font-size: 1.05rem;
        }

        .hero-cta .btn {
            min-width: 190px;
        }

        .hero-mini-chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .55rem .8rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
            color: white;
            font-size: .9rem;
            border: 1px solid rgba(255, 255, 255, .16);
        }

        .home-feature-card {
            background: white;
            border-radius: 22px;
            padding: 1.5rem;
            box-shadow: 0 15px 35px rgba(15, 23, 42, .08);
            border: 1px solid #eef2ff;
        }

        .feature-icon {
            width: 52px;
            height: 52px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary), #38bdf8);
            color: white;
            font-size: 1.25rem;
        }

        .about-hero {
            position: relative;
            padding: 5rem 0 4rem;
            background: linear-gradient(135deg, rgba(37, 99, 235, .95), rgba(59, 130, 246, .85));
            color: white;
            overflow: hidden;
        }

        .about-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top left, rgba(59, 130, 246, .45), transparent 30%),
                radial-gradient(circle at bottom right, rgba(255, 255, 255, .18), transparent 25%);
            pointer-events: none;
        }

        .about-hero .content-panel {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 28px;
            padding: 2.5rem;
            box-shadow: 0 30px 70px rgba(15, 23, 42, .18);
        }

        .about-hero h1,
        .about-hero h2,
        .about-hero p {
            color: white;
        }

        .about-feature-card,
        .about-value-card {
            border-radius: 24px;
            border: 1px solid rgba(15, 23, 42, .08);
            box-shadow: 0 24px 60px rgba(15, 23, 42, .08);
            background: white;
        }

        .about-feature-card .icon,
        .about-value-card .icon {
            width: 54px;
            height: 54px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary), #38bdf8);
            color: white;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .about-feature-card h5,
        .about-value-card h5 {
            margin-bottom: .75rem;
            color: var(--dark);
        }

        .about-section {
            padding: 1rem 0 1.5rem;
            max-width: 1180px;
            margin: 0 auto;
        }

        .about-divider {
            height: 1px;
            background: rgba(15, 23, 42, .08);
            margin: 2.5rem 0;
        }

        .product-hero {
            position: relative;
            padding: 5rem 0 3rem;
            background: linear-gradient(135deg, #1d4ed8, #2563eb);
            color: white;
            overflow: hidden;
        }

        .product-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(255, 255, 255, .18), transparent 20%),
                radial-gradient(circle at bottom left, rgba(59, 130, 246, .16), transparent 20%);
            pointer-events: none;
        }

        .product-hero .product-hero-card {
            position: relative;
            z-index: 1;
            border-radius: 28px;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .2);
            backdrop-filter: blur(12px);
            padding: 2rem;
            box-shadow: 0 30px 80px rgba(15, 23, 42, .16);
        }

        .product-hero .product-badge {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .6rem 1rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, .16);
            border: 1px solid rgba(255, 255, 255, .22);
            color: white;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .product-stat-card {
            border-radius: 24px;
            padding: 1.5rem;
            background: white;
            border: 1px solid #eef2ff;
            box-shadow: 0 20px 50px rgba(15, 23, 42, .08);
        }

        .product-stat-card p {
            margin-bottom: .5rem;
            color: #64748b;
        }

        .product-stat-card h4 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }

        .product-background {
            background: radial-gradient(circle at top left, rgba(37, 99, 235, .08), transparent 20%),
                radial-gradient(circle at bottom right, rgba(56, 189, 248, .08), transparent 18%);
        }

        .product-background .container {
            max-width: 1180px;
            margin: 0 auto;
        }

        .product-search-bar .form-control,
        .product-search-bar .form-select,
        .product-search-bar .btn {
            border-radius: 16px;
        }

        .product-search-bar .form-control {
            border-right: none;
            min-height: 58px;
        }

        .product-search-bar .form-select {
            min-height: 58px;
            padding-left: 1.25rem;
            padding-right: 1.25rem;
        }

        .product-search-bar .input-group .btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            min-height: 58px;
        }

        .product-search-bar .form-select,
        .product-search-bar .btn {
            box-shadow: 0 15px 35px rgba(15, 23, 42, .06);
        }

        #product-list-row {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
            max-width: 1300px;
            margin: 0 auto;
        }

        .product-grid-wrapper {
            justify-content: center;
        }

        @media (max-width: 992px) {

            .product-search-bar .input-group,
            .product-search-bar .form-select {
                width: 100%;
            }
        }

        .product-search-bar .row {
            justify-content: center;
        }

        .product-card {
            min-height: 420px;
            border-radius: 24px;
            overflow: hidden;
            transition: transform .28s ease, box-shadow .28s ease;
        }

        .product-card .card-header {
            min-height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 24px 24px 0 0;
        }

        .product-card .card-body {
            padding: 1.7rem;
        }

        .product-card {
            border: none;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 70px rgba(15, 23, 42, .08);
            transition: transform .28s ease, box-shadow .28s ease;
        }

        .product-card .product-label {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .55rem 1rem;
            border-radius: 999px;
            color: white;
            font-weight: 600;
        }

        .product-card .card-body {
            padding: 1.7rem;
        }

        .product-card .price {
            margin-top: 1rem;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--secondary);
        }

        .product-card .btn-detail {
            width: 100%;
            border-radius: 14px;
            padding: .85rem 1rem;
            font-weight: 600;
        }

        .product-badge-small {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .45rem .75rem;
            border-radius: 999px;
            background: rgba(15, 23, 42, .05);
            font-size: .85rem;
            color: #334155;
        }

        /*================ CARD =================*/

        .card {

            border: none;

            border-radius: 22px;

            overflow: hidden;

            box-shadow:

                0 15px 35px rgba(0, 0, 0, .08);

        }

        .card:hover {

            transform: translateY(-6px);

        }

        /*================ BUTTON =================*/

        .btn {

            border-radius: 12px;

            padding: 10px 20px;

            font-weight: 600;

        }

        .btn-primary {

            background: var(--primary);

            border: none;

        }

        .btn-primary:hover {

            background: #1d4ed8;

            transform: translateY(-2px);

        }

        /*================ SECTION =================*/

        .section-box {

            background: white;

            border-radius: 20px;

            padding: 30px;

            box-shadow:

                0 15px 35px rgba(0, 0, 0, .06);

        }

        .section-box:hover {

            transform: translateY(-5px);

        }

        /*================ ALERT =================*/

        .alert {

            border: none;

            border-radius: 15px;

        }

        /*================ FORM =================*/

        .form-control {

            border-radius: 12px;

            padding: 12px;

        }

        .form-control:focus {

            border-color: #2563eb;

            box-shadow:

                0 0 0 .2rem rgba(37, 99, 235, .15);

        }

        /*================ SCROLL =================*/

        ::-webkit-scrollbar {

            width: 8px;

        }

        ::-webkit-scrollbar-thumb {

            background: #2563eb;

            border-radius: 30px;

        }

        ::-webkit-scrollbar-track {

            background: #edf2f7;

        }

        /*================ FOOTER =================*/

        footer {

            background: #0f172a;

            color: white;

            padding: 40px;

            margin-top: 60px;

            text-align: center;

        }

        /*================ ANIMATION =================*/

        .fade-up {

            opacity: 1;

            transform: translateY(0);

        }

        .fade-up.show {

            opacity: 1;

            transform: translateY(0);

            transition: .8s;

        }

        /*================ RESPONSIVE =================*/

        @media(max-width:768px) {

            .navbar-goa {

                width: 96%;

                padding: 10px 15px;

            }

            .navbar-brand {

                font-size: 24px;

            }

            .main-content {

                margin-top: 55px;

            }

        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        nav.navbar.navbar-expand-lg .navbar-collapse,
        nav.navbar.navbar-expand-lg .navbar-collapse.collapse:not(.show) {
            display: flex !important;
            justify-content: flex-end !important;
            align-items: center !important;
        }

        nav.navbar.navbar-expand-lg .navbar-nav {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
        }
    </style>

</head>

<body>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <!-- ================= NAVBAR ================= -->

    <nav class="navbar navbar-light navbar-goa">

        <div class="container d-flex align-items-center justify-content-between">

            <a class="navbar-brand" href="/">

                <span style="color:#ff7a00;">Apo</span><span style="color:#2563eb;">tek</span>24

            </a>

            <div class="d-flex align-items-center gap-3">

                <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                    <i class="bi bi-house-door me-1"></i>
                    Home
                </a>

                <a href="/tentang" class="nav-link {{ request()->is('tentang') ? 'active' : '' }}">
                    <i class="bi bi-info-circle me-1"></i>
                    Tentang
                </a>

                <a href="/produk" class="nav-link {{ request()->is('produk*') ? 'active' : '' }}">
                    <i class="bi bi-capsule-pill me-1"></i>
                    Produk
                </a>

                <a href="/rekomendasi" class="nav-link {{ request()->is('rekomendasi*') ? 'active' : '' }}">
                    <i class="bi bi-search-heart me-1"></i>
                    Rekomendasi
                </a>

                <a href="/laporan" class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text me-1"></i>
                    Laporan
                </a>

                <a href="/kontak" class="nav-link {{ request()->is('kontak') ? 'active' : '' }}">
                    <i class="bi bi-envelope me-1"></i>
                    Kontak
                </a>

            </div>

        </div>

    </nav>

    <!-- ================= CONTENT ================= -->

    <div class="main-content">

        <div class="container py-4" style="padding-top: 0.75rem;">

            @if(session('success'))

            <div class="alert alert-success shadow-sm alert-dismissible fade show">

                <i class="bi bi-check-circle-fill me-2"></i>

                {{ session('success') }}

                <button class="btn-close"
                    data-bs-dismiss="alert"></button>

            </div>

            @endif

            @if(session('error'))

            <div class="alert alert-danger shadow-sm alert-dismissible fade show">

                <i class="bi bi-exclamation-circle-fill me-2"></i>

                {{ session('error') }}

                <button class="btn-close"
                    data-bs-dismiss="alert"></button>

            </div>

            @endif

        </div>

        @yield('content')

    </div>

    <!-- ================= FOOTER ================= -->

    <footer>

        <div class="container">

            <div class="row align-items-center">

                <div class="col-md-4 text-md-start text-center mb-4 mb-md-0">

                    <h5 class="fw-semibold">

                        Menu

                    </h5>

                    <p>

                        Home<br>

                        Produk<br>

                        Rekomendasi<br>

                        Kontak

                    </p>

                </div>

                <div class="col-md-4 text-center mb-4 mb-md-0">

                    <h4 class="fw-bold mb-3">

                        <span style="color:#ff7a00;">Apo</span><span style="color:#60a5fa;">tek</span>24

                    </h4>

                    <p class="mt-0 opacity-75">

                        Platform rekomendasi obat berbasis Rule Based,
                        TF-IDF dan Cosine Similarity untuk membantu
                        pengguna menemukan obat yang sesuai.

                    </p>

                </div>

                <div class="col-md-4 text-md-end text-center">

                    <h5 class="fw-semibold">

                        Kontak

                    </h5>

                    <p>

                        📧 admin@apotek24.id

                        <br>

                        📍 Indonesia

                    </p>

                </div>

            </div>

            <hr class="border-secondary">

            <div class="text-center opacity-75">

                © 2026 Apotek24 • Sistem Rekomendasi Obat

            </div>

        </div>

    </footer>
    @stack('scripts')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>