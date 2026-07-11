<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Apotek24 - User</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        * {

            margin: 0;

            padding: 0;

            box-sizing: border-box;

        }

        body {

            background: #f5f6fa;

            font-family: 'Segoe UI', sans-serif;

            color: #333;

        }

        /* ================= NAVBAR ================= */

        .navbar-user {

            position: fixed;

            top: 0;

            left: 50%;

            transform: translateX(-50%);

            width: 92%;

            max-width: 1450px;

            background: linear-gradient(135deg, rgba(15, 23, 42, .96), rgba(30, 64, 175, .96));

            border: 1px solid rgba(255, 255, 255, .14);

            backdrop-filter: blur(18px);

            border-radius: 28px;

            padding: 15px 28px;

            box-shadow: 0 24px 55px rgba(15, 23, 42, .2);

            z-index: 9999;

        }

        .navbar-brand {

            font-size: 36px;

            font-weight: 700;

            color: #eef2ff;

            letter-spacing: .03em;

        }

        .navbar-brand span:first-child {

            color: #93c5fd;

        }

        .navbar-brand span:last-child {

            color: #e0f2fe;

        }

        .navbar-user .navbar-toggler {

            border: 1px solid rgba(255, 255, 255, .24);

            border-radius: 14px;

            padding: 7px 10px;

        }

        .navbar-user .navbar-toggler-icon {

            filter: invert(1);

        }

        .navbar-nav {

            gap: 12px;

        }

        .navbar-nav .nav-link {

            color: #4b5563 !important;

            font-weight: 600;

            padding: 10px 18px;

            border-radius: 12px;

            transition: .3s;

        }

        .navbar-nav .nav-link:hover {

            background: #2d6cdf;

            color: #fff !important;

        }

        .navbar-nav .active {

            background: #2d6cdf;

            color: #fff !important;

        }

        .btn-user {

            background: #2d6cdf;

            color: white;

            border: none;

            padding: 12px 30px;

            border-radius: 15px;

            font-weight: bold;

            transition: .3s;

        }

        .btn-user:hover {

            background: #1b58c7;

        }

        /* ================= CONTENT ================= */

        .main-content {

            padding-top: 24px;

            padding-bottom: 20px;

            min-height: calc(100vh - 90px);

        }

        .page-card {

            background: white;

            border-radius: 25px;

            padding: 45px;

            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);

        }

        .glass-box {

            background: white;

            border-radius: 20px;

            padding: 30px;

            box-shadow: 0 8px 25px rgba(0, 0, 0, .06);

        }

        .form-control {

            height: 58px;

            border-radius: 14px;

            border: 1px solid #d8dce2;

        }

        .form-control:focus {

            box-shadow: none;

            border-color: #2d6cdf;

        }

        .btn-primary {

            height: 58px;

            border-radius: 14px;

            font-weight: bold;

            background: #2d6cdf;

            border: none;

        }

        .btn-primary:hover {

            background: #2458b9;

        }

        .card {

            border: none;

            border-radius: 20px;

            overflow: hidden;

            box-shadow: 0 8px 20px rgba(0, 0, 0, .08);

            transition: .3s;

        }

        .card:hover {

            transform: translateY(-4px);

        }

        .container {

            max-width: 1450px;

        }

        /* ================= FOOTER ================= */

        footer {

            background: #0f172a;

            color: #fff;

            margin-top: 30px;

            padding: 50px 0 20px;

        }

        footer h5 {

            font-weight: bold;

            margin-bottom: 15px;

        }

        footer p {

            color: #cbd5e1;

            margin-bottom: 8px;

        }

        footer a {

            color: #fff;

            text-decoration: none;

            transition: .3s;

        }

        footer a:hover {

            color: #60a5fa;

        }

        .copyright {

            margin-top: 30px;

            padding-top: 20px;

            border-top: 1px solid rgba(255, 255, 255, .15);

            text-align: center;

            color: #cbd5e1;

        }

        /* ================= ALERT ================= */

        .alert {

            border: none;

            border-radius: 15px;

        }

        /* ================= ANIMATION ================= */

        .fade-up {

            opacity: 0;

            transform: translateY(25px);

            transition: .6s;

        }

        .fade-up.show {

            opacity: 1;

            transform: translateY(0);

        }

        /* ================= RESPONSIVE ================= */

        @media(max-width:991px) {

            .navbar-user {

                width: 96%;

                border-radius: 20px;

                padding: 12px 20px;

            }

            .navbar-brand {

                font-size: 30px;

            }

            .navbar-user .navbar-nav {

                gap: 8px;

            }

            .navbar-user .nav-link,

            .navbar-user .btn-user {

                padding: 10px 14px;

                font-size: 0.96rem;

            }

            .main-content {

                padding-top: 35px;

            }

            .page-card {

                padding: 25px;

            }

            .navbar-user .navbar-collapse {

                background: rgba(15, 23, 42, .92);

                border-radius: 20px;

                margin-top: 12px;

                padding: 16px;

            }

            .navbar-user .navbar-nav.mx-auto {

                flex-direction: column;

                align-items: stretch;

            }

            .navbar-user .navbar-nav.ms-auto {

                margin-top: 12px;

            }

        }
    </style>

</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-user">

        <div class="container-fluid">

            <a class="navbar-brand" href="{{ route('home') }}">

                <span>Apo</span><span>tek24</span>

            </a>

            <button
                type="button"
                class="navbar-toggler"
                data-bs-toggle="collapse"
                data-bs-target="#navbarUser"
                aria-controls="navbarUser"
                aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div

                class="collapse navbar-collapse"

                id="navbarUser">

                <ul class="navbar-nav mx-auto">

                    <li class="nav-item">

                        <a

                            href="{{ route('home') }}"

                            class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">

                            Home

                        </a>

                    </li>

                    <li class="nav-item">

                        <a

                            href="{{ route('produk') }}"

                            class="nav-link {{ request()->is('produk*') ? 'active' : '' }}">

                            Produk

                        </a>

                    </li>

                    <li class="nav-item">

                        <a

                            href="{{ route('rekomendasi') }}"

                            class="nav-link {{ request()->is('rekomendasi*') ? 'active' : '' }}">

                            Rekomendasi

                        </a>

                    </li>

                    <li class="nav-item">

                        <a

                            href="{{ route('laporan') }}"

                            class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}">

                            Laporan

                        </a>

                    </li>

                </ul>

            </div>

        </div>

    </nav>

    <div class="main-content">

        <div class="container">

            @if(session('success'))

            <div class="alert alert-success fade-up">

                {{ session('success') }}

            </div>

            @endif

            @if(session('error'))

            <div class="alert alert-danger fade-up">

                {{ session('error') }}

            </div>

            @endif

            @yield('content')

        </div>

    </div>
    <footer>

        <div class="container">

            <div class="row">

                <div class="col-lg-4 mb-4">

                    <h5>Apotek24</h5>

                    <p>

                        Sistem Rekomendasi Obat menggunakan
                        Rule Based, TF-IDF dan Cosine Similarity
                        untuk membantu pengguna menemukan
                        obat yang sesuai dengan gejala.

                    </p>

                </div>

                <div class="col-lg-4 mb-4">

                    <h5>Menu</h5>

                    <p>

                        <a href="/user">

                            Home

                        </a>

                    </p>

                    <p>

                        <a href="/user/produk">

                            Produk

                        </a>

                    </p>

                    <p>

                        <a href="/user/rekomendasi">

                            Rekomendasi

                        </a>

                    </p>

                    <p>

                        <a href="/laporan">

                            Laporan

                        </a>

                    </p>

                </div>

                <div class="col-lg-4 mb-4">

                    <h5>Metode</h5>

                    <p>

                        ✔ Rule Based

                    </p>

                    <p>

                        ✔ TF-IDF

                    </p>

                    <p>

                        ✔ Cosine Similarity

                    </p>

                </div>

            </div>

            <div class="copyright">

                © 2026 Apotek24 | Sistem Rekomendasi Obat

            </div>

        </div>

    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const elements = document.querySelectorAll(".fade-up");

            function showAnimation() {

                elements.forEach(function(el) {

                    const position = el.getBoundingClientRect().top;

                    const screen = window.innerHeight;

                    if (position < screen - 80) {

                        el.classList.add("show");

                    }

                });

            }

            showAnimation();

            window.addEventListener(

                "scroll",

                showAnimation

            );

        });
    </script>

</body>

</html>