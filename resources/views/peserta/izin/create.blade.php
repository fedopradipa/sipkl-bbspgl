@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
          <h5 class="mb-0 fw-bold">Formulir Pengajuan Izin</h5>
        </div>
        <div class="card-body p-4">
          <form action="{{ route('izin.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Jenis Izin --}}
            <div class="mb-3">
              <label class="form-label fw-bold">Jenis Izin</label>
              <div class="d-flex gap-3">
                <div class="form-check card-radio">
                  <input class="form-check-input" type="radio" name="jenis_izin" id="izinSakit" value="sakit" required>
                  <label class="form-check-label" for="izinSakit">Sakit</label>
                </div>
                <div class="form-check card-radio">
                  <input class="form-check-input" type="radio" name="jenis_izin" id="izinLain" value="izin">
                  <label class="form-check-label" for="izinLain">Kepentingan Lain</label>
                </div>
              </div>
            </div>

            {{-- Tanggal --}}
            <div class="row">
              <div class="col-6 mb-3">
                <label class="form-label small text-muted">Dari Tanggal</label>
                <input type="date" name="tgl_mulai" class="form-control" required>
              </div>
              <div class="col-6 mb-3">
                <label class="form-label small text-muted">Sampai Tanggal</label>
                <input type="date" name="tgl_selesai" class="form-control" required>
              </div>
            </div>

            {{-- Alasan --}}
            <div class="mb-3">
              <label class="form-label fw-bold">Alasan</label>
              <textarea name="alasan" class="form-control" rows="3" placeholder="Jelaskan alasan izin Anda..." required></textarea>
            </div>

            {{-- Upload Bukti --}}
            <div class="mb-4">
              <label class="form-label fw-bold">Bukti Pendukung (Surat Dokter/Foto)</label>
              <input type="file" name="bukti_foto" class="form-control" accept="image/*,.pdf">
              <small class="text-muted">Format: JPG, PNG, PDF. Maks 2MB.</small>
            </div>

            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary btn-lg fw-bold">Kirim Pengajuan</button>
              <a href="{{ route('izin.index') }}" class="btn btn-light text-muted">Batal</a>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection