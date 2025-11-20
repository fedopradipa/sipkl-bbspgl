@extends('layouts.admin') {{-- Sesuaikan dengan layout utama Anda --}}

@section('content')
<div class="row">
    {{-- Kolom Kiri: Foto Profil & Info Dasar --}}
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Profil Pengguna</h6>
            </div>
            <div class="card-body text-center">
                {{-- Tampilkan foto profil ATAU placeholder inisial --}}
                @if ($user->foto_profil)
                    <img src="{{ asset('storage/foto_profil/' . $user->foto_profil) }}" alt="Foto Profil" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <img src="https://placehold.co/150x150/e2e8f0/6c757d?text={{ strtoupper(substr($user->name, 0, 1)) }}" alt="Foto Profil" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                @endif
                
                <h5 class="my-3">{{ $user->name }}</h5>
                
                {{-- Tampilkan Jabatan (jika ada), jika tidak, tampilkan Role --}}
                <p class="text-muted mb-1">{{ $user->jabatan ?? $user->role->nama_role }}</p>
                <p class="text-muted mb-4">NIP: {{ $user->nip ?? '-' }}</p>

                <div class="d-flex justify-content-center gap-2">
                     <a href="{{ route('user.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                     <a href="{{ route('user.edit', $user->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-pencil-alt me-1"></i> Edit Pengguna Ini
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Detail Akun --}}
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Detail Akun</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-3">
                        <p class="mb-0 text-muted">Nama Lengkap</p>
                    </div>
                    <div class="col-sm-9">
                        <p class="mb-0 fw-bold">{{ $user->name }}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <p class="mb-0 text-muted">Email</p>
                    </div>
                    <div class="col-sm-9">
                        <p class="mb-0">{{ $user->email }}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <p class="mb-0 text-muted">Peran (Role)</p>
                    </div>
                    <div class="col-sm-9">
                        <span class="badge bg-primary fs-6">{{ $user->role->nama_role ?? 'Tidak ada role' }}</span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <p class="mb-0 text-muted">Jabatan</p>
                    </div>
                    <div class="col-sm-9">
                        <p class="mb-0">{{ $user->jabatan ?? '-' }}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <p class="mb-0 text-muted">NIP</p>
                    </div>
                    <div class="col-sm-9">
                        <p class="mb-0">{{ $user->nip ?? '-' }}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <p class="mb-0 text-muted">Akun Dibuat</p>
                    </div>
                    <div class="col-sm-9">
                        <p class="mb-0">{{ $user->created_at->isoFormat('D MMMM YYYY, HH:mm') }}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <p class="mb-0 text-muted">Terakhir Diperbarui</p>
                    </div>
                    <div class="col-sm-9">
                        <p class="mb-0">{{ $user->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection