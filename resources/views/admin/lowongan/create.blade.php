@extends('layouts.admin')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Tambah Lowongan Baru</h5>
        <form action="{{ route('lowongan.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="judul" class="form-label">Judul Lowongan <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul') }}" placeholder="Contoh: Web Developer">
                @error('judul')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="divisi" class="form-label">Divisi <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('divisi') is-invalid @enderror" id="divisi" name="divisi" value="{{ old('divisi') }}" placeholder="Contoh: Teknologi Informasi">
                @error('divisi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="kuota" class="form-label">Kuota <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('kuota') is-invalid @enderror" id="kuota" name="kuota" value="{{ old('kuota', 1) }}" min="1">
                @error('kuota')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi Lowongan</label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Validasi Periode --}}
            <fieldset class="border p-3 mb-3">
                <legend class="w-auto h6">Periode Pendaftaran</legend>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="window_start_date" class="form-label">Tanggal Mulai Pendaftaran <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('window_start_date') is-invalid @enderror" id="window_start_date" name="window_start_date" value="{{ old('window_start_date') }}">
                        @error('window_start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="window_end_date" class="form-label">Tanggal Selesai Pendaftaran <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('window_end_date') is-invalid @enderror" id="window_end_date" name="window_end_date" value="{{ old('window_end_date') }}">
                        @error('window_end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="min_duration" class="form-label">Durasi Minimal (Bulan) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('min_duration') is-invalid @enderror" id="min_duration" name="min_duration" value="{{ old('min_duration', 1) }}" min="1">
                    @error('min_duration')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </fieldset>

            {{-- Validasi Hari & Jam Kerja --}}
            <fieldset class="border p-3 mb-3">
                <legend class="w-auto h6">Aturan Kerja</legend>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="is_fulltime" name="is_fulltime" value="1" {{ old('is_fulltime', 1) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_fulltime">
                        Wajib Masuk Senin-Jumat (Full-time)
                    </label>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="jam_masuk_standar" class="form-label">Jam Masuk Standar <span class="text-danger">*</span></label>
                        <input type="time" class="form-control @error('jam_masuk_standar') is-invalid @enderror" id="jam_masuk_standar" name="jam_masuk_standar" value="{{ old('jam_masuk_standar', '08:00') }}">
                        @error('jam_masuk_standar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="jam_pulang_standar" class="form-label">Jam Pulang Standar <span class="text-danger">*</span></label>
                        <input type="time" class="form-control @error('jam_pulang_standar') is-invalid @enderror" id="jam_pulang_standar" name="jam_pulang_standar" value="{{ old('jam_pulang_standar', '16:00') }}">
                        @error('jam_pulang_standar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </fieldset>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="is_open" name="is_open" value="1" {{ old('is_open', 1) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_open">
                    Buka Lowongan
                </label>
            </div>
            
            <a href="{{ route('lowongan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Simpan
            </button>

        </form>
    </div>
</div>
@endsection
