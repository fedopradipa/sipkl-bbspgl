@extends('layouts.admin')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        
        {{-- Menghapus card-title dan <hr> agar sesuai dengan template acuan --}}

        <form action="{{ route('role.update', $role->id_role) }}" method="POST">
            @method('PUT')
            @csrf

            {{-- Input untuk Nama Peran --}}
            <div class="mb-3">
                <label for="nama_role" class="form-label">Nama Peran (Role) <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama_role') is-invalid @enderror" id="nama_role" name="nama_role" value="{{ old('nama_role', $role->nama_role) }}" placeholder="Contoh: superadmin">
                
                {{-- Menampilkan error validasi --}}
                @error('nama_role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            {{-- Tombol Aksi --}}
            <a href="{{ route('role.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-1"></i> Simpan Perubahan
            </button>

        </form>
    </div>
</div>
@endsection