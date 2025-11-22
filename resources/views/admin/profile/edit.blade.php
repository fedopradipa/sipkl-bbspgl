@extends('layouts.app') {{-- Sesuaikan dengan layout utama Anda --}}

@section('content')
<div class="row">
    {{-- Kolom Kiri: Foto Profil --}}
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Foto Profil</h6>
            </div>
            <div class="card-body text-center">
                {{-- Tampilkan foto saat ini ATAU placeholder --}}
                @if (Auth::user()->foto_profil)
                {{-- DIPERBARUI: class "img-fluid" DIHAPUS --}}
                <img id="foto-preview" src="{{ asset('storage/foto_profil/' . Auth::user()->foto_profil) }}" alt="Foto Profil" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                @else
                {{-- DIPERBARUI: class "img-fluid" DIHAPUS, style DITAMBAHKAN, dan ?text= DITAMBAHKAN KEMBALI --}}
                <img id="foto-preview" src="https://placehold.co/150x150/e2e8f0/6c757d?text={{ strtoupper(substr(Auth::user()->name, 0, 1)) }}" alt="Foto Profil" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                @endif

                <h5 class="my-3">{{ Auth::user()->name }}</h5>
                <p class="text-muted mb-1">{{ Auth::user()->jabatan ?? Auth::user()->role->nama_role }}</p>
                <p class="text-muted mb-4">NIP: {{ Auth::user()->nip ?? '-' }}</p>

                {{-- Form (bagian dari form utama) untuk upload file --}}
                <label for="foto_profil" class="btn btn-primary btn-sm">
                    <i class="fas fa-upload me-1"></i> Ganti Foto
                </label>
                {{-- Input file disembunyikan tapi terhubung ke form utama --}}
                <input type="file" id="foto_profil" name="foto_profil" class="d-none @error('foto_profil') is-invalid @enderror" form="profile-form">
                @error('foto_profil')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Detail Form --}}
    <div class="col-lg-8">
        {{-- Pesan Sukses --}}
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        {{-- Form Utama --}}
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profile-form">
            @csrf
            @method('PUT')

            {{-- Card 1: Edit Biodata --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Edit Biodata</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nip" class="form-label">NIP (Opsional)</label>
                            <input type="text" class="form-control @error('nip') is-invalid @enderror" id="nip" name="nip" value="{{ old('nip', $user->nip) }}" placeholder="Masukkan NIP Anda...">
                            @error('nip')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jabatan" class="form-label">Jabatan (Opsional)</label>
                            <input type="text" class="form-control @error('jabatan') is-invalid @enderror" id="jabatan" name="jabatan" value="{{ old('jabatan', $user->jabatan) }}" placeholder="Misal: Teknisi, Admin Logistik...">
                            @error('jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Ubah Password --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Ubah Password (Opsional)</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Isi jika ingin mengganti password">
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Ketik ulang password baru">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('foto_profil');
        const preview = document.getElementById('foto-preview');

        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
@endpush