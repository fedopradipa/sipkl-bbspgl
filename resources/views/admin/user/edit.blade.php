@extends('layouts.app') {{-- Sesuaikan dengan layout utama Anda --}}

@section('content')
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">{{ $header ?? 'Form Edit Pengguna' }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('user.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- Wajib untuk update --}}

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                        id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                        id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="role_id" class="form-label">Peran (Role) <span class="text-danger">*</span></label>
                    <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                        <option value="" disabled>Pilih peran...</option>
                        @foreach($roles as $role)
                        {{-- Pilih 'old()' atau data '$user' yang ada --}}
                        <option value="{{ $role->id_role }}" {{ old('role_id', $user->role_id) == $role->id_role ? 'selected' : '' }}>
                            {{ $role->nama_role }}
                        </option>
                        @endforeach
                    </select>
                    @error('role_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr class="my-3">
            <h6 class="mb-3 text-muted">Ubah Password (Opsional)</h6>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password Baru</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                        id="password" name="password" placeholder="Isi hanya jika ingin mengganti">
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Ketik ulang password baru">
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="mt-4 border-top pt-3">
                <a href="{{ route('user.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>
@endsection