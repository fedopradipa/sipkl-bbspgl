@extends('layouts.auth') {{-- Menggunakan layout 'guest' (sesuai template Anda) --}}

@section('content')
<div class="card-body p-4 p-md-5">
    <!-- Header -->
    <div class="auth-card-header">
        <i class="fas fa-box-open fa-3x"></i> {{-- Mengganti ikon store dengan box --}}
        <h2 class="mb-1">Selamat Datang</h2>
        <p class="text-muted">Login Sistem Inventaris BBSPGL</p>
    </div>

    <!-- Login Form -->
    {{-- 
      PERHATIKAN:
      Rute 'login' ini akan kita buat di web.php selanjutnya.
      Controller Anda (LoginController@login) sudah siap menanganinya.
    --}}
    <form action="{{ route('login') }}" method="POST">
        {{-- Ini adalah bagian terpenting untuk mengatasi error 419 --}}
        @csrf

        <!-- Email Input -->
        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                {{-- 
                  LoginController@login akan mengirimkan error 
                  validasi ke key 'email' jika gagal
                --}}
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" 
                       placeholder="contoh@email.com" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus>
            </div>
            {{-- Menampilkan error validasi dari LoginController --}}
            @error('email')
                <div class="text-danger mt-1 small">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Password Input -->
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
        
        <!-- Remember Me (Opsional, tapi bagus) -->
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label" for="remember">Ingat saya</label>
        </div>


        <!-- Submit Button -->
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Masuk</button>
        </div>
    </form>

    <!-- Footer -->
    <div class="text-center auth-card-footer">
        {{-- 
          PERUBAHAN (Sesuai Model 114/149):
          Link 'Daftar di sini' dihapus karena ini adalah sistem internal.
        --}}
        <p class="text-muted mb-0">Sistem Manajemen Inventaris BBSPGL</p>
    </div>
</div>
@endsection