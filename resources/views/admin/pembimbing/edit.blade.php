@extends('layouts.app')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Edit Data Pembimbing</h5>
        {{-- Form untuk update, pastikan method POST dan action ke route update --}}
        <form action="{{ route('pembimbing.update', $pembimbing->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- Method spoofing untuk request PUT --}}

            {{-- Field Nama --}}
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $pembimbing->nama) }}" placeholder="Contoh: John Doe">
                @error('nama')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Field Jabatan --}}
            <div class="mb-3">
                <label for="jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('jabatan') is-invalid @enderror" id="jabatan" name="jabatan" value="{{ old('jabatan', $pembimbing->jabatan) }}" placeholder="Contoh: Analis Kimia">
                @error('jabatan')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Field No. HP --}}
            <div class="mb-3">
                <label for="no_hp" class="form-label">No. HP (WhatsApp)</label>
                <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" value="{{ old('no_hp', $pembimbing->no_hp) }}" placeholder="Contoh: 081234567890">
                <div class="form-text">Nomor HP digunakan untuk komunikasi via WhatsApp. Awali dengan 08.</div>
                @error('no_hp')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Checkbox Status Aktif --}}
            <div class="form-check mb-3">
                {{--
                  Logika checked:
                  1. Cek apakah ada input lama (old('is_active')). Jika ada, gunakan itu.
                  2. Jika tidak ada input lama, gunakan nilai dari database ($pembimbing->is_active).
                --}}
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $pembimbing->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                    Status Aktif
                </label>
                <div class="form-text">Pembimbing yang tidak aktif tidak akan bisa dipilih saat proses penerimaan.</div>
            </div>

            {{-- Tombol Aksi --}}
            <a href="{{ route('pembimbing.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Simpan Perubahan
            </button>

        </form>
    </div>
</div>
@endsection