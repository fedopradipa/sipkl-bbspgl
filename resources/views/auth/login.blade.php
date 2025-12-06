@extends('layouts.auth')

@section('content')
<div class="card-body p-4 p-md-5">
    <div class="auth-card-header">
        <div class="logo-container">
            <img src="{{ asset('images/logo.png') }}" alt="Logo SIPKL BBSPGL" class="img-fluid login-logo">
        </div>
        <h2 class="mb-1">Selamat Datang</h2>
        <p class="text-muted">Login Sistem SIPKL</p>
    </div>

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                    id="email" name="email"
                    placeholder="contoh@email.com"
                    value="{{ old('email') }}"
                    required
                    autofocus>
            </div>
            @error('email')
            <div class="text-danger mt-1 small">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">Kata Sandi</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control @error('password') is-invalid @enderror"
                    id="password" name="password"
                    placeholder="Masukkan kata sandi"
                    required>
            </div>
            @error('password')
            <div class="text-danger mt-1 small">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label" for="remember">Ingat saya</label>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Masuk</button>
        </div>
    </form>

    <div class="text-center auth-card-footer mt-4">
        {{-- TAMBAHAN: Link ke halaman Register --}}
        <p class="mb-2">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">
                Daftar di sini
            </a>
        </p>

        <p class="text-muted mb-0 small">Sistem Informasi PKL BBSPGL</p>
    </div>
</div>
@endsection