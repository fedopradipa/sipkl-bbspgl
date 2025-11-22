@extends('layouts.app')

@section('content')
{{-- Menghapus class 'shadow-sm' agar konsisten --}}
<div class="card mb-4">
    {{-- Menghapus 'card-header' agar sesuai template acuan --}}
    <div class="card-body">
        <form action="{{ route('role.store') }}" method="POST">
            @csrf

            {{-- Input untuk Nama Peran --}}
            <div class="mb-3">
                <label for="nama_role" class="form-label">Nama Peran (Role) <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama_role') is-invalid @enderror"
                    id="nama_role" name="nama_role"
                    placeholder="Contoh: superadmin"
                    value="{{ old('nama_role') }}"
                    autofocus>

                {{-- Menampilkan error validasi --}}
                @error('nama_role')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tombol Aksi --}}
            <a href="{{ route('role.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Batal
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-1"></i> Simpan Peran
            </button>

        </form>
    </div>
</div>
@endsection