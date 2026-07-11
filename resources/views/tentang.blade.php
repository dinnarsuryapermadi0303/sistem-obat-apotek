@extends('layout')

@section('content')

<section class="about-hero">
    <div class="container">
        <div class="content-panel mx-auto" style="max-width: 960px;">
            <div class="row align-items-center">
                <div class="col-lg-7 mb-4 mb-lg-0">
                    <span class="badge bg-white text-primary mb-3">Tentang Kami</span>
                    <h1 class="display-5 fw-bold">Apotek 24 — Solusi Kesehatan Digital Anda</h1>
                    <p class="lead opacity-85">Kami hadir untuk memudahkan pencarian obat dan memberikan rekomendasi terbaik dengan teknologi yang mudah diakses.</p>
                </div>
                <div class="col-lg-5 text-lg-end text-center">
                    <div class="p-4 rounded-4 border border-white border-opacity-20" style="backdrop-filter: blur(10px); background: rgba(255,255,255,.1);">
                        <h2 class="fw-bold">+1000</h2>
                        <p class="mb-0 opacity-85">Konsultasi kesehatan dan rekomendasi obat setiap hari</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container about-section">
    <div class="row gy-4">
        <div class="col-lg-5">
            <div class="about-feature-card p-4 h-100">
                <div class="icon">
                    <i class="bi bi-award"></i>
                </div>
                <h5>Visi</h5>
                <p>Menjadi apotek digital terpercaya yang memberikan solusi kesehatan secara cepat, tepat, dan akurat.</p>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="about-value-card p-4 h-100">
                <div class="icon">
                    <i class="bi bi-lightbulb"></i>
                </div>
                <h5>Misi</h5>
                <ul class="mb-0">
                    <li>Menyediakan obat berkualitas dan terpercaya</li>
                    <li>Memberikan pelayanan yang cepat dan ramah</li>
                    <li>Mengembangkan sistem rekomendasi berbasis teknologi</li>
                    <li>Membantu masyarakat dalam memilih obat yang tepat</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="about-divider"></div>

    <div class="row gy-4">
        <div class="col-lg-4">
            <div class="about-feature-card p-4 h-100">
                <div class="icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h5>Keamanan</h5>
                <p>Informasi obat yang jelas dan akurat untuk pengguna membuat pilihan lebih aman dan terarah.</p>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="about-feature-card p-4 h-100">
                <div class="icon">
                    <i class="bi bi-speedometer2"></i>
                </div>
                <h5>Kecepatan</h5>
                <p>Proses rekomendasi berjalan cepat, sehingga pengguna mendapatkan solusi dalam waktu singkat.</p>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="about-feature-card p-4 h-100">
                <div class="icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <h5>Layanan</h5>
                <p>Memberikan pengalaman yang ramah dan mudah, sehingga setiap pengguna merasa terbantu.</p>
            </div>
        </div>
    </div>
</section>

@endsection