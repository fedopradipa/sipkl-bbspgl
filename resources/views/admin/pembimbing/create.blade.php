@extends('layouts.admin')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Tambah Pembimbing Baru</h5>
        <form action="{{ route('pembimbing.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" placeholder="Contoh: John Doe">
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('jabatan') is-invalid @enderror" id="jabatan" name="jabatan" value="{{ old('jabatan') }}" placeholder="Contoh: Analis Kimia">
                @error('jabatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="no_hp" class="form-label">No. HP (WhatsApp)</label>
                <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890">
                <div class="form-text">Nomor HP digunakan untuk komunikasi via WhatsApp. Awali dengan 08.</div>
                @error('no_hp')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                    Status Aktif
                </label>
                <div class="form-text">Pembimbing yang tidak aktif tidak akan bisa dipilih saat proses penerimaan.</div>
            </div>
            
            <a href="{{ route('pembimbing.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Simpan
            </button>

        </form>
    </div>
</div>
@endsection
