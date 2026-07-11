@extends('layout')

@section('content')

<section class="hero-goa">
    <div class="overlay"></div>

    <div class="container hero-content">
        <div class="row align-items-center g-5">

            <div class="col-lg-5">
                <div class="hero-panel">
                    <span class="hero-badge">
                        <i class="bi bi-capsule"></i>
                        Layanan cepat dan terpercaya
                    </span>

                    <h3 class="fw-bold mb-3">
                        Temukan obat dan rekomendasi terbaik dengan lebih mudah
                    </h3>

                    <p class="text-muted mb-4">
                        Sistem kami membantu pelanggan mendapatkan rekomendasi yang lebih relevan, aman, dan cepat sesuai kebutuhan kesehatan.
                    </p>

                    <div class="hero-stat-grid">
                        <div class="hero-stat">
                            <strong>24/7</strong>
                            <small class="text-muted">Layanan pendampingan</small>
                        </div>
                        <div class="hero-stat">
                            <strong>100%</strong>
                            <small class="text-muted">Informasi jelas</small>
                        </div>
                        <div class="hero-stat">
                            <strong>Fast</strong>
                            <small class="text-muted">Proses rekomendasi</small>
                        </div>
                        <div class="hero-stat">
                            <strong>Safe</strong>
                            <small class="text-muted">Pilihan obat terarah</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 text-white">
                <span class="hero-badge">
                    <i class="bi bi-shield-check"></i>
                    Pelayanan modern untuk kebutuhan sehari-hari
                </span>

                <h1 class="fw-bold display-4 mb-2">
                    Platform
                </h1>
                <h1 class="fw-bold display-4 mb-3">
                    Apotek <span class="text-warning">24</span>
                </h1>

                <h2 class="fw-bold display-6 mb-3 animated-text">
                    <span id="changing-text">Terpercaya</span>
                    <span id="icon">✦</span>
                </h2>

                <p class="lead text-light mb-4">
                    Dapatkan rekomendasi obat yang lebih praktis, aman, dan sesuai dengan gejala yang Anda rasakan.
                </p>

                <div class="hero-cta d-flex flex-wrap gap-3 mb-4">
                    <a href="/rekomendasi" class="btn btn-primary btn-lg">
                        <i class="bi bi-stars"></i> Coba Rekomendasi
                    </a>
                    <a href="/tentang" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-info-circle"></i> Tentang Kami
                    </a>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <span class="hero-mini-chip"><i class="bi bi-heart-pulse"></i> Rekomendasi tepat</span>
                    <span class="hero-mini-chip"><i class="bi bi-bag-check"></i> Obat lengkap</span>
                    <span class="hero-mini-chip"><i class="bi bi-chat-dots"></i> Informasi jelas</span>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="container py-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="home-feature-card h-100">
                <div class="feature-icon">
                    <i class="bi bi-capsule"></i>
                </div>
                <h5 class="fw-bold mt-3">Rekomendasi Obat</h5>
                <p class="text-muted mb-0">Membantu menemukan obat yang paling sesuai dengan keluhan Anda secara lebih terarah.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="home-feature-card h-100">
                <div class="feature-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h5 class="fw-bold mt-3">Aman dan Terpercaya</h5>
                <p class="text-muted mb-0">Informasi yang jelas untuk mendukung keputusan penggunaan obat yang lebih aman.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="home-feature-card h-100">
                <div class="feature-icon">
                    <i class="bi bi-lightning-charge"></i>
                </div>
                <h5 class="fw-bold mt-3">Cepat Diproses</h5>
                <p class="text-muted mb-0">Proses rekomendasi berjalan cepat sehingga Anda tidak perlu menunggu lama.</p>
            </div>
        </div>
    </div>
</section>

<script>
    const words = ['Terpercaya', 'Cepat', 'Aman', 'Praktis'];
    const textEl = document.getElementById('changing-text');
    let index = 0;

    setInterval(() => {
        index = (index + 1) % words.length;
        if (textEl) {
            textEl.textContent = words[index];
        }
    }, 2400);
</script>

@endsection