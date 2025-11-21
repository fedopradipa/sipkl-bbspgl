@extends('layouts.app')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      {{-- Header Info Lowongan --}}
      <div class="card border-0 shadow-sm mb-4 bg-primary text-white">
        <div class="card-body p-4">
          <h4 class="fw-bold mb-1">Formulir Pendaftaran PKL</h4>
          <p class="mb-0 opacity-75">Anda melamar untuk posisi:</p>
          <h2 class="fw-bold mt-2">{{ $lowongan->judul }}</h2>
          <div class="mt-3">
            <span class="badge bg-white text-primary me-2">{{ $lowongan->divisi }}</span>
            <span class="badge bg-warning text-dark">Min. {{ $lowongan->min_duration }} Bulan</span>
          </div>
        </div>
      </div>

      {{-- Alert Error Validasi Custom --}}
      @if($errors->has('min_duration'))
      <div class="alert alert-danger d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <div>{{ $errors->first('min_duration') }}</div>
      </div>
      @endif

      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <form action="{{ route('lowongan.store', $lowongan->id) }}" method="POST">
            @csrf

            <h5 class="fw-bold text-secondary mb-3"><i class="fas fa-calendar-alt me-2"></i>Rencana Jadwal</h5>

            <div class="row mb-4">
              <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Mulai (Usulan) <span class="text-danger">*</span></label>
                <input type="date" name="tgl_mulai_usulan" class="form-control @error('tgl_mulai_usulan') is-invalid @enderror" value="{{ old('tgl_mulai_usulan') }}">
                <small class="text-muted">Min: {{ $lowongan->window_start_date->format('d M Y') }}</small>
                @error('tgl_mulai_usulan') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Selesai (Usulan) <span class="text-danger">*</span></label>
                <input type="date" name="tgl_selesai_usulan" class="form-control @error('tgl_selesai_usulan') is-invalid @enderror" value="{{ old('tgl_selesai_usulan') }}">
                @error('tgl_selesai_usulan') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <hr class="my-4">

            <h5 class="fw-bold text-secondary mb-3"><i class="fas fa-clock me-2"></i>Preferensi Hari Kerja</h5>

            {{-- LOGIKA TAMPILAN BERDASARKAN TIPE LOWONGAN --}}
            @if($lowongan->is_fulltime)
            {{-- CASE A: FULLTIME (Read Only) --}}
            <div class="alert alert-info border-0 d-flex align-items-center">
              <i class="fas fa-info-circle fa-lg me-3"></i>
              <div>
                <strong>Lowongan Full-Time</strong><br>
                Sesuai ketentuan, hari kerja wajib: <u>Senin - Jumat</u>.
              </div>
            </div>
            {{-- Input Hidden agar form tetap valid jika logic berubah, walau di controller kita hardcode juga --}}
            <input type="hidden" name="is_fulltime_agreement" value="1">

            @else
            {{-- CASE B: FLEXIBLE (Checkbox) --}}
            <p class="text-muted mb-3">Lowongan ini bersifat <strong>Flexible</strong>. Silakan pilih hari dimana Anda bersedia hadir:</p>

            <div class="mb-3">
              <label class="form-label d-block fw-bold">Pilih Hari: <span class="text-danger">*</span></label>
              <div class="btn-group d-flex flex-wrap" role="group">
                @foreach(['Monday'=>'Senin', 'Tuesday'=>'Selasa', 'Wednesday'=>'Rabu', 'Thursday'=>'Kamis', 'Friday'=>'Jumat'] as $dayEng => $dayInd)
                <input type="checkbox" class="btn-check" id="btn-{{ $dayEng }}" name="hari_kerja[]" value="{{ $dayEng }}"
                  {{ (is_array(old('hari_kerja')) && in_array($dayEng, old('hari_kerja'))) ? 'checked' : '' }}>
                <label class="btn btn-outline-primary m-1 rounded" for="btn-{{ $dayEng }}">{{ $dayInd }}</label>
                @endforeach
              </div>
              @error('hari_kerja') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Alasan Pemilihan Hari <span class="text-danger">*</span></label>
              <textarea name="alasan_hari_kerja" class="form-control @error('alasan_hari_kerja') is-invalid @enderror" rows="3" placeholder="Contoh: Saya ada kuliah di hari Selasa dan Kamis...">{{ old('alasan_hari_kerja') }}</textarea>
              @error('alasan_hari_kerja') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            @endif

            <div class="d-grid gap-2 mt-5">
              <button type="submit" class="btn btn-success btn-lg fw-bold">
                <i class="fas fa-paper-plane me-2"></i> Kirim Lamaran
              </button>
              <a href="{{ route('guest.lowongan.show', $lowongan->id) }}" class="btn btn-light text-muted">Batal</a>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection