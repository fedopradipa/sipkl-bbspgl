@extends('layouts.admin') {{-- Sesuaikan dengan layout utama Anda --}}

@section('content')
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">{{ $header ?? 'Form Input Pengguna Baru' }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('user.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="role_id" class="form-label">Peran (Role) <span class="text-danger">*</span></label>
                    <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                        <option value="" disabled selected>Pilih peran...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id_role }}" {{ old('role_id') == $role->id_role ? 'selected' : '' }}>
                                {{ $role->nama_role }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- --- TAMBAHAN BARU (Sesuai Model 189) --- --}}
            <hr class="my-3">
            <h6 class="mb-3 text-muted">Data Opsional (Profil)</h6>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nip" class="form-label">NIP</label>
                    <input type="text" class="form-control @error('nip') is-invalid @enderror" 
                           id="nip" name="nip" value="{{ old('nip') }}" placeholder="NIP Pegawai (jika ada)">
                    @error('nip')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="jabatan" class="form-label">Jabatan</label>
                    <input type="text" class="form-control @error('jabatan') is-invalid @enderror" 
                           id="jabatan" name="jabatan" value="{{ old('jabatan') }}" placeholder="Misal: Teknisi, Admin Logistik...">
                    @error('jabatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            {{-- --- AKHIR TAMBAHAN --- --}}

            <hr class="my-3">
            <h6 class="mb-3 text-muted">Password</h6>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="mt-4 border-top pt-3">
                <a href="{{ route('user.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Simpan Pengguna Baru
                </button>
            </div>

        </form>
    </div>
</div>
@endsection