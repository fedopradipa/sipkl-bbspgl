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

      {{-- Alert Error Validasi Global --}}
      @if($errors->any())
      <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-2 fs-4"></i>
        <div>
          <strong>Terjadi Kesalahan!</strong> Silakan periksa kembali isian form Anda di bawah ini.
        </div>
      </div>
      @endif

      <div class="card border-0 shadow-sm">
        <div class="card-body p-4 p-md-5">
          {{-- PENTING: Tambahkan enctype="multipart/form-data" untuk upload file --}}
          <form action="{{ route('lamaran.store', $lowongan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- BAGIAN 1: JADWAL --}}
            <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
              <i class="fas fa-calendar-alt me-2"></i>Rencana Jadwal
            </h5>

            <div class="row mb-4">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Tanggal Mulai (Usulan) <span class="text-danger">*</span></label>
                <input type="date" name="tgl_mulai_usulan" class="form-control @error('tgl_mulai_usulan') is-invalid @enderror" value="{{ old('tgl_mulai_usulan') }}">
                <div class="form-text text-muted">
                  <i class="fas fa-info-circle me-1"></i>
                  Paling cepat: {{ $lowongan->window_start_date->format('d M Y') }}
                </div>
                @error('tgl_mulai_usulan') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Tanggal Selesai (Usulan) <span class="text-danger">*</span></label>
                <input type="date" name="tgl_selesai_usulan" class="form-control @error('tgl_selesai_usulan') is-invalid @enderror" value="{{ old('tgl_selesai_usulan') }}">
                @error('tgl_selesai_usulan') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              {{-- Error Min Duration --}}
              @if($errors->has('min_duration'))
              <div class="col-12">
                <div class="alert alert-danger small py-2">
                  {{ $errors->first('min_duration') }}
                </div>
              </div>
              @endif
            </div>

            {{-- BAGIAN 2: PREFERENSI HARI --}}
            <h5 class="fw-bold text-primary mb-3 border-bottom pb-2 mt-5">
              <i class="fas fa-clock me-2"></i>Preferensi Hari Kerja
            </h5>

            @if($lowongan->is_fulltime)
            {{-- CASE A: FULLTIME --}}
            <div class="alert alert-info border-0 d-flex align-items-center mb-4">
              <i class="fas fa-business-time fa-2x me-3"></i>
              <div>
                <strong>Lowongan Full-Time (WFO)</strong><br>
                Peserta wajib masuk kantor setiap hari kerja: <u>Senin - Jumat</u>.
              </div>
            </div>
            <input type="hidden" name="is_fulltime_agreement" value="1">
            @else
            {{-- CASE B: FLEXIBLE --}}
            <p class="text-muted mb-3">Lowongan ini bersifat <strong>Flexible</strong>. Silakan pilih hari dimana Anda bersedia hadir:</p>

            <div class="mb-3">
              <label class="form-label fw-bold">Pilih Hari: <span class="text-danger">*</span></label>
              <div class="btn-group d-flex flex-wrap" role="group">
                @foreach(['Monday'=>'Senin', 'Tuesday'=>'Selasa', 'Wednesday'=>'Rabu', 'Thursday'=>'Kamis', 'Friday'=>'Jumat', 'Saturday' => 'Sabtu',
                'Sunday' => 'Minggu'] as $dayEng => $dayInd)
                <input type="checkbox" class="btn-check" id="btn-{{ $dayEng }}" name="hari_kerja[]" value="{{ $dayEng }}"
                  {{ (is_array(old('hari_kerja')) && in_array($dayEng, old('hari_kerja'))) ? 'checked' : '' }}>
                <label class="btn btn-outline-primary m-1 rounded" for="btn-{{ $dayEng }}">{{ $dayInd }}</label>
                @endforeach
              </div>
              @error('hari_kerja') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
              <label class="form-label fw-bold">Alasan Pemilihan Hari <span class="text-danger">*</span></label>
              <textarea name="alasan_hari_kerja" class="form-control @error('alasan_hari_kerja') is-invalid @enderror" rows="3" placeholder="Contoh: Saya ada kuliah di hari Selasa dan Kamis...">{{ old('alasan_hari_kerja') }}</textarea>
              @error('alasan_hari_kerja') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            @endif

            {{-- BAGIAN 3: DOKUMEN PENDUKUNG (BARU) --}}
            <h5 class="fw-bold text-primary mb-3 border-bottom pb-2 mt-5">
              <i class="fas fa-file-alt me-2"></i>Dokumen Pendukung
            </h5>

            <div class="mb-4">
              <label for="file_surat_pengantar" class="form-label fw-bold">
                Surat Pengantar Sekolah/Kampus <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <input type="file"
                  class="form-control @error('file_surat_pengantar') is-invalid @enderror"
                  id="file_surat_pengantar"
                  name="file_surat_pengantar"
                  accept=".pdf,.jpg,.jpeg,.png">
                <label class="input-group-text" for="file_surat_pengantar"><i class="fas fa-upload"></i></label>
              </div>
              <div class="form-text text-muted mt-2">
                <ul class="mb-0 ps-3">
                  <li>Wajib melampirkan surat resmi dari sekolah/kampus.</li>
                  <li>Format file: <strong>PDF, JPG, PNG</strong>.</li>
                  <li>Ukuran maksimal: <strong>2 MB</strong>.</li>
                </ul>
              </div>
              @error('file_surat_pengantar')
              <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            {{-- TOMBOL SUBMIT --}}
            <div class="d-grid gap-2 mt-5">
              <button type="submit" class="btn btn-success btn-lg fw-bold shadow-sm py-3">
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