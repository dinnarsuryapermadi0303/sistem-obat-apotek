@extends('layout')

@section('content')

<div class="container mt-5">

    <div class="row justify-content-center">
        <div class="col-12 text-center mb-4 fade-up">
            <h2 class="fw-bold">Kontak Kami</h2>
            <p class="text-muted">Silakan hubungi tim Apotek24 jika Anda memerlukan bantuan atau ingin menyampaikan masukan.</p>
        </div>
    </div>

    <div class="row gy-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="mb-3">Informasi Kontak</h5>
                <p class="mb-3">Kami siap membantu Anda kapan saja. Kirim pesan melalui WhatsApp dan tim kami akan merespons sesegera mungkin.</p>

                <div class="mb-3">
                    <h6 class="mb-1">Nomor WhatsApp</h6>
                    <p class="mb-0">+62 858-0721-6066</p>
                </div>

                <div class="mb-3">
                    <h6 class="mb-1">Email</h6>
                    <p class="mb-0">admin@apotek24.id</p>
                </div>

                <div class="mb-3">
                    <h6 class="mb-1">Alamat</h6>
                    <p class="mb-0">Indonesia</p>
                </div>

                <div class="alert alert-info mt-4 mb-0">
                    <strong>Catatan:</strong> Form ini akan membuka WhatsApp dan mengirim pesan ke admin.
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h5 class="mb-3">Kirim Pesan</h5>
                <form id="wa-contact-form" novalidate>
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" id="nama" class="form-control" placeholder="Nama Anda" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" class="form-control" placeholder="email@contoh.com" required>
                    </div>

                    <div class="mb-3">
                        <label for="pesan" class="form-label">Pesan</label>
                        <textarea id="pesan" class="form-control" rows="6" placeholder="Tulis pesan atau keluhan Anda di sini..." required></textarea>
                    </div>

                    <button type="button" id="btn-kirim-pesan" class="btn btn-primary w-100">
                        Kirim ke WhatsApp Admin 🚀
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nomorAdmin = '6285807216066';
        const btnKirim = document.getElementById('btn-kirim-pesan');
        const namaField = document.getElementById('nama');
        const emailField = document.getElementById('email');
        const pesanField = document.getElementById('pesan');

        function showError(message, field) {
            alert(message);
            if (field) {
                field.focus();
            }
        }

        btnKirim.addEventListener('click', function() {
            const nama = namaField.value.trim();
            const email = emailField.value.trim();
            const pesan = pesanField.value.trim();

            if (!nama) {
                showError('Silakan isi nama Anda.', namaField);
                return;
            }

            if (!email) {
                showError('Silakan isi email Anda.', emailField);
                return;
            }

            if (!pesan) {
                showError('Silakan isi pesan Anda.', pesanField);
                return;
            }

            const fullMessage = `Nama: ${nama}\nEmail: ${email}\n\nPesan:\n${pesan}`;
            const encodedMessage = encodeURIComponent(fullMessage);
            const url = `https://wa.me/${nomorAdmin}?text=${encodedMessage}`;

            window.location.href = url;
        });
    });
</script>
@endpush