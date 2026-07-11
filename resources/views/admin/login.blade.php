@extends('layout-admin')

@section('content')

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-6 col-md-8">

            <div class="card shadow border-0 rounded-4 login-card">

                <div class="card-body p-5">
                    <div class="text-center mb-5">
                        <div class="login-badge mb-3 mx-auto">
                            <!-- Health logo SVG -->
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="bi bi-heart-pulse-fill text-primary">
                                <rect width="24" height="24" rx="6" fill="#eef6ff" />
                                <path d="M6.516 10.08c.89-2.08 3.07-3.18 4.98-3.18 1.91 0 4.09 1.1 4.98 3.18.69 1.61.1 3.48-1.44 4.45L12 19.5l-2.63-4.975c-1.54-.97-2.13-2.84-1.44-4.45z" fill="#0d6efd" />
                                <path d="M10 11l1-2 1 2 1-4 1 4" stroke="#fff" stroke-width=".8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <h2 class="fw-bold mb-2">Login Admin</h2>
                        <p class="text-muted mb-4">Masuk untuk mengelola panel admin Apotek 24.</p>
                    </div>

                    {{-- ERROR --}}
                    @if($errors->any())

                    <div class="alert alert-danger">

                        <ul class="mb-0">

                            @foreach($errors->all() as $error)

                            <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                    </div>

                    @endif

                    {{-- FORM LOGIN --}}
                    <form action="{{ route('admin.login.post') }}"
                        method="POST">

                        @csrf

                        {{-- EMAIL --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Email Admin
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                placeholder="Masukkan email admin"
                                value="{{ old('email') }}"
                                required>
                        </div>

                        {{-- PASSWORD --}}
                        <div class="mb-4">

                            <label class="form-label">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Masukkan password"
                                required>
                        </div>

                        {{-- BUTTON --}}
                        <button
                            type="submit"
                            class="btn btn-primary w-100 py-2">
                            Masuk Sekarang
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection