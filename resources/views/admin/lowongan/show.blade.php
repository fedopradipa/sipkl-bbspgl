@extends('layouts.app')

@section('content')
<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title m-0 fw-bold text-primary"><i class="fas fa-eye me-2"></i>Detail Lowongan</h5>
        <span class="badge {{ $lowongan->is_open ? 'bg-success' : 'bg-danger' }}">
            {{ $lowongan->is_open ? 'PUBLISHED (BUKA)' : 'DRAFT (TUTUP)' }}
        </span>
    </div>
    <div class="card-body">

        {{-- 1. INFORMASI DASAR --}}
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold">posisi Lowongan</label>
                <input type="text" class="form-control bg-light" value="{{ $lowongan->posisi }}" disabled>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Deskripsi Lowongan</label>
            <textarea class="form-control bg-light" rows="4" disabled>{{ $lowongan->deskripsi }}</textarea>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold">Jurusan Terkait</label>
                <input type="text" class="form-control bg-light" value="{{ $lowongan->jurusan_terkait ?? '-' }}" disabled>
            </div>
        </div>

        {{-- 2. PERIODE --}}
        <fieldset class="border p-3 mb-4 rounded bg-light">
            <legend class="w-auto h6 fw-bold text-uppercase text-secondary px-2"><i class="far fa-calendar-alt me-1"></i> Periode & Durasi</legend>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tanggal Mulai Pendaftaran</label>
                    <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($lowongan->window_start_date)->translatedFormat('d F Y') }}" disabled>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tanggal Selesai Pendaftaran</label>
                    <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($lowongan->window_end_date)->translatedFormat('d F Y') }}" disabled>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Durasi Minimal</label>
                    <input type="text" class="form-control" value="{{ $lowongan->min_duration }} Bulan" disabled>
                </div>
            </div>
        </fieldset>

        {{-- 3. ATURAN KERJA --}}
        <fieldset class="border p-3 mb-4 rounded bg-light">
            <legend class="w-auto h6 fw-bold text-uppercase text-secondary px-2"><i class="fas fa-clock me-1"></i> Aturan & Jadwal Kerja</legend>

            <div class="mb-3">
                <label class="form-label fw-bold">Tipe Jadwal PKL</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" disabled {{ $lowongan->is_fulltime ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold {{ $lowongan->is_fulltime ? 'text-primary' : 'text-secondary' }}">
                        {{ $lowongan->is_fulltime ? 'Jadwal Terikat (Ditentukan Admin)' : 'Jadwal Fleksibel (Peserta Mengajukan)' }}
                    </label>
                </div>
            </div>

            @if($lowongan->is_fulltime)
            <div class="mb-3 p-3 border rounded bg-white">
                <label class="form-label small text-uppercase text-secondary fw-bold mb-2">Hari Kerja Wajib:</label>
                <div class="d-flex flex-wrap gap-3">
                    @php
                    $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                    $savedDays = $lowongan->hari_kerja ?? [];
                    @endphp
                    @foreach($days as $day)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" disabled {{ is_array($savedDays) && in_array($day, $savedDays) ? 'checked' : '' }}>
                        <label class="form-check-label {{ is_array($savedDays) && in_array($day, $savedDays) ? 'text-dark fw-bold' : 'text-muted' }}">
                            {{ $day }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- JAM KERJA (tanpa toleransi) --}}
            <div class="row mt-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-success">Jam Buka Absen</label>
                    <input type="time" class="form-control" value="{{ \Carbon\Carbon::parse($lowongan->jam_buka_absen)->format('H:i') }}" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-danger">Jam Tutup Absen</label>
                    <input type="time" class="form-control" value="{{ \Carbon\Carbon::parse($lowongan->jam_tutup_absen)->format('H:i') }}" disabled>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Jam Masuk Resmi</label>
                    <input type="time" class="form-control" value="{{ \Carbon\Carbon::parse($lowongan->jam_masuk_standar)->format('H:i') }}" disabled>
                </div>

            </div>
        </fieldset>

        <div class="d-flex gap-2">
            <a href="{{ route('lowongan.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
            <a href="{{ route('lowongan.edit', $lowongan->id) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i> Edit</a>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="fas fa-trash me-1"></i> Hapus</button>
        </div>
    </div>
</div>

{{-- MODAL HAPUS --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus lowongan <strong>{{ $lowongan->posisi }}</strong>? <br><br>
                <small class="text-danger">Tindakan ini tidak dapat dibatalkan.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('lowongan.destroy', $lowongan->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus Permanen</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection