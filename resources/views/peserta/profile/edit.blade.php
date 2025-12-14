@extends('layouts.app')

@section('content')
<div class="row">

  {{-- Kolom Kiri: Foto Profil & Info Singkat --}}
  <div class="col-lg-4">
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-light">
        <h6 class="mb-0">Foto Profil</h6>
      </div>
      <div class="card-body text-center">

        {{-- Logic Foto Profil (Diperbaiki) --}}
        @php
        $currentPhotoName = $profil->foto_profil ?? null;
        $photoUrl = $currentPhotoName
        ? asset('storage/foto_profil/' . $currentPhotoName)
        : 'https://placehold.co/150x150/e2e8f0/6c757d?text=' . strtoupper(substr(Auth::user()->name, 0, 1));
        @endphp

        <img id="foto-preview" src="{{ $photoUrl }}" alt="Foto Profil" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">

        <h5 class="my-3">{{ Auth::user()->name }}</h5>

        {{-- Menampilkan Info Institusi/Jurusan (Sintaks diperbaiki) --}}
        <p class="text-muted mb-1">
          {{ $profil->institusi ?? 'Belum ada Institusi' }}
        </p>
        <p class="text-muted mb-4 badge bg-info text-white">
          {{ $profil->jurusan ?? 'Peserta' }}
        </p>

        {{-- Tombol Ganti Foto --}}
        <label for="foto_profil" class="btn btn-primary btn-sm">
          <i class="fas fa-upload me-1"></i> Ganti Foto
        </label>
        {{-- Input file terhubung ke form utama --}}
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
    <form action="{{ route('profile.peserta.update') }}" method="POST" enctype="multipart/form-data" id="profile-form">
      @csrf
      @method('PUT')

      {{-- Card 1: Informasi Akun (Tabel Users) --}}
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
          <h6 class="mb-0">Informasi Akun</h6>
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
        </div>
      </div>

      {{-- Card 2: Data Lengkap Peserta (Tabel $profils) --}}
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
          <h6 class="mb-0">Data Akademik & Kontak</h6>
        </div>
        <div class="card-body">
          <div class="row">
            {{-- Kolom NIM/NIS --}}
            <div class="col-md-6 mb-3">
              <label for="nim_nis" class="form-label">NIM / NIS <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('nim_nis') is-invalid @enderror" id="nim_nis" name="nim_nis"
                value="{{ old('nim_nis', $profil->nim_nis ?? '') }}" placeholder="Nomor Induk...">
              @error('nim_nis')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            {{-- Kolom No HP --}}
            <div class="col-md-6 mb-3">
              <label for="no_hp" class="form-label">No. WhatsApp / HP <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp"
                value="{{ old('no_hp', $profil->no_hp ?? '') }}" placeholder="08...">
              @error('no_hp')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row">
            {{-- Kolom Institusi --}}
            <div class="col-md-6 mb-3">
              <label for="institusi" class="form-label">Asal Sekolah / Kampus <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('institusi') is-invalid @enderror" id="institusi" name="institusi"
                value="{{ old('institusi', $profil->institusi ?? '') }}" placeholder="Nama Instansi...">
              @error('institusi')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            {{-- Kolom Jurusan --}}
            <div class="col-md-6 mb-3">
              <label for="jurusan" class="form-label">Jurusan / Prodi <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('jurusan') is-invalid @enderror" id="jurusan" name="jurusan"
                value="{{ old('jurusan', $profil->jurusan ?? '') }}" placeholder="Jurusan...">
              @error('jurusan')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          {{-- Kolom Alamat --}}
          <div class="mb-3">
            <label for="alamat" class="form-label">Alamat Domisili</label>
            <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3" placeholder="Alamat lengkap...">{{ old('alamat', $profil->alamat ?? '') }}</textarea>
            @error('alamat')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>

      {{-- Card 3: Ubah Password --}}
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

{{-- Script Preview Image --}}
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