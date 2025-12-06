@extends('layouts.auth')

@section('content')
<div class="card-body p-4 p-md-5">

    <div class="auth-card-header mb-4">
        <i class="fas fa-user-plus fa-3x text-primary mb-2"></i>
        <h2 class="mb-1">Daftar Akun</h2>
        <p class="text-muted">Lengkapi data untuk memulai PKL</p>
    </div>

    <form action="{{ route('register') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    placeholder="Sesuai KTP" value="{{ old('name') }}" required>
            </div>
            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Alamat Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    placeholder="contoh@email.com" value="{{ old('email') }}" required>
            </div>
            @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="row g-2 mb-2">
            <div class="col-md-12">
                <label class="form-label">Kata Sandi</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                        placeholder="Min. 8 karakter" required>
                </div>
            </div>
            <div class="col-md-12">
                <label class="form-label">Ulangi Sandi</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-check"></i></span>
                    <input type="password" name="password_confirmation" class="form-control"
                        placeholder="Ketik ulang" required>
                </div>
            </div>
        </div>
        @error('password') <div class="text-danger small mt-1 mb-3">{{ $message }}</div> @enderror

        <div class="my-4 text-center position-relative">
            <hr class="text-muted opacity-25">
            <span class="position-absolute top-50 start-50 translate-middle bg-white px-2 text-muted small">DATA PESERTA</span>
        </div>

        <div class="row g-3">

            <div class="col-md-12">
                <label class="form-label">Asal Sekolah/Kampus</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-university"></i></span>
                    <input type="text" name="institusi" class="form-control @error('institusi') is-invalid @enderror"
                        placeholder="Nama Instansi" value="{{ old('institusi') }}" required>
                </div>
            </div>

            <div class="col-md-12">
                <label class="form-label">NIM / NIS</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                    <input type="text" name="nim_nis" class="form-control @error('nim_nis') is-invalid @enderror"
                        placeholder="Nomor Induk" value="{{ old('nim_nis') }}" required>
                </div>
            </div>

            <div class="col-md-12">
                <label class="form-label">Jurusan</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-book"></i></span>
                    <input type="text" name="jurusan" class="form-control @error('jurusan') is-invalid @enderror"
                        placeholder="Jurusan" value="{{ old('jurusan') }}" required>
                </div>
            </div>

            <div class="col-md-12">
                <label class="form-label">No. WhatsApp</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror"
                        placeholder="08xxxxxxxxxx" value="{{ old('no_hp') }}" required>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label">Alamat Domisili</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                    <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror"
                        rows="2" placeholder="Alamat lengkap..." required>{{ old('alamat') }}</textarea>
                </div>
            </div>
        </div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary">Daftar Sekarang</button>
        </div>
    </form>

    <div class="text-center auth-card-footer mt-4">
        <p class="mb-0">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">
                Masuk di sini
            </a>
        </p>
    </div>

</div>
@endsection