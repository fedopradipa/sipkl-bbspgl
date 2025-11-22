@extends('layouts.app')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">{{ $header }}</h5>

        <div class="mb-3">
            <label for="judul" class="form-label">Judul Lowongan</label>
            <input type="text" class="form-control" id="judul" name="judul" value="{{ $lowongan->judul }}" disabled>
        </div>

        <div class="mb-3">
            <label for="divisi" class="form-label">Divisi</label>
            <input type="text" class="form-control" id="divisi" name="divisi" value="{{ $lowongan->divisi }}" disabled>
        </div>

        <div class="mb-3">
            <label for="kuota" class="form-label">Kuota</label>
            <input type="number" class="form-control" id="kuota" name="kuota" value="{{ $lowongan->kuota }}" disabled>
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi Lowongan</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5" disabled>{{ $lowongan->deskripsi }}</textarea>
        </div>

        <fieldset class="border p-3 mb-3">
            <legend class="w-auto h6">Periode Pendaftaran</legend>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="window_start_date" class="form-label">Tanggal Mulai Pendaftaran</label>
                    <input type="date" class="form-control" id="window_start_date" name="window_start_date" value="{{ $lowongan->window_start_date }}" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="window_end_date" class="form-label">Tanggal Selesai Pendaftaran</label>
                    <input type="date" class="form-control" id="window_end_date" name="window_end_date" value="{{ $lowongan->window_end_date }}" disabled>
                </div>
            </div>
            <div class="mb-3">
                <label for="min_duration" class="form-label">Durasi Minimal (Bulan)</label>
                <input type="number" class="form-control" id="min_duration" name="min_duration" value="{{ $lowongan->min_duration }}" disabled>
            </div>
        </fieldset>

        <fieldset class="border p-3 mb-3">
            <legend class="w-auto h6">Aturan Kerja</legend>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="is_fulltime" name="is_fulltime" value="1" {{ $lowongan->is_fulltime ? 'checked' : '' }} disabled>
                <label class="form-check-label" for="is_fulltime">
                    Wajib Masuk Senin-Jumat (Full-time)
                </label>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="jam_masuk_standar" class="form-label">Jam Masuk Standar</label>
                    <input type="time" class="form-control" id="jam_masuk_standar" name="jam_masuk_standar" value="{{ \Carbon\Carbon::parse($lowongan->jam_masuk_standar)->format('H:i') }}" disabled>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="jam_pulang_standar" class="form-label">Jam Pulang Standar</label>
                    <input type="time" class="form-control" id="jam_pulang_standar" name="jam_pulang_standar" value="{{ \Carbon\Carbon::parse($lowongan->jam_pulang_standar)->format('H:i') }}" disabled>
                </div>
            </div>
        </fieldset>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="is_open" name="is_open" value="1" {{ $lowongan->is_open ? 'checked' : '' }} disabled>
            <label class="form-check-label" for="is_open">
                Buka Lowongan (Publish)
            </label>
        </div>

        <a href="{{ route('lowongan.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
        <a href="{{ route('lowongan.edit', $lowongan->id) }}" class="btn btn-primary">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
            <i class="fas fa-trash me-1"></i> Hapus
        </button>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus lowongan <strong>{{ $lowongan->judul }}</strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('lowongan.destroy', $lowongan->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection