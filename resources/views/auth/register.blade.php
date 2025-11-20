@extends('layouts.auth')

@section('content')
<div class="card-body p-4 p-md-5">
    <!-- Header with Icon and Title -->
    <div class="auth-card-header">
        <i class="fas fa-user-plus fa-3x"></i>
        <h2 class="mb-1">Buat Akun Baru</h2>
        <p class="text-muted">Isi form di bawah untuk mendaftar</p>
    </div>

    <!-- Registration Form -->
    <form action="#" method="POST">
        @csrf

        <!-- Full Name Input -->
        <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Masukkan nama lengkap Anda" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <!-- Email Input -->
        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="contoh@email.com" value="{{ old('email') }}" required>
                 @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <!-- Password Input -->
        <div class="mb-3">
            <label for="password" class="form-label">Kata Sandi</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Buat kata sandi" required>
                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        
        <!-- Confirm Password Input -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Ulangi kata sandi" required>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Daftar</button>
        </div>
    </form>

    <!-- Footer for Sign In -->
    <div class="text-center auth-card-footer">
        <p class="text-muted mb-0">Sudah punya akun? <a href="#">Masuk di sini</a></p>
    </div>
</div> 
@endsection
