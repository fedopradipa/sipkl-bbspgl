@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

  {{-- HEADER: Tombol Kembali & posisi --}}
  <div class="d-flex align-items-center justify-content-between mb-4 mt-4">
    <div>
      <a href="{{ route('admin.alumni.index') }}" class="text-decoration-none text-muted small hover-arrow mb-1 d-inline-block">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Arsip
      </a>
      <h4 class="fw-bold text-dark m-0">Detail Alumni PKL</h4>
    </div>

    {{-- Status Badge Besar --}}
    @if($item->status == 'selesai')
    <span class="badge bg-success px-3 py-2 fs-6 rounded-pill">
      <i class="fas fa-check-circle me-2"></i> LULUS PROGRAM
    </span>
    @else
    <span class="badge bg-danger px-3 py-2 fs-6 rounded-pill">
      <i class="fas fa-ban me-2"></i> DROP OUT
    </span>
    @endif
  </div>

  <div class="row g-4">

    {{-- KOLOM KIRI: Profil & Ringkasan --}}
    <div class="col-lg-4">
      {{-- Kartu Profil --}}
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body text-center p-4">
          {{-- Avatar --}}
          @php
          $foto = $item->pengguna->profile->foto_profil ?? null;
          $avatar = $foto ? asset('storage/foto_profil/'.$foto) : 'https://ui-avatars.com/api/?name='.urlencode($item->pengguna->name).'&background=random&color=fff';
          @endphp
          <img src="{{ $avatar }}" class="rounded-circle mb-3 border shadow-sm" width="100" height="100" style="object-fit: cover;">

          <h5 class="fw-bold mb-1">{{ $item->pengguna->name }}</h5>
          <p class="text-muted small mb-3">{{ $item->pengguna->email }}</p>

          <hr>

          <div class="text-start">
            <div class="mb-3">
              <label class="small text-muted fw-bold text-uppercase">Institusi</label>
              <div class="fw-bold text-dark">{{ $item->pengguna->profile->institusi ?? '-' }}</div>
            </div>
            <div class="mb-3">
              <label class="small text-muted fw-bold text-uppercase">Jurusan</label>
              <div class="text-dark">{{ $item->pengguna->profile->jurusan ?? '-' }}</div>
            </div>
            <div>
              <label class="small text-muted fw-bold text-uppercase">Kontak</label>
              <div class="d-flex align-items-center gap-2">
                <a href="https://wa.me/{{ $item->pengguna->profile->no_hp ?? '' }}" target="_blank" class="btn btn-sm btn-success w-100">
                  <i class="fab fa-whatsapp me-1"></i> WhatsApp
                </a>
                <a href="mailto:{{ $item->pengguna->email }}" class="btn btn-sm btn-outline-secondary w-100">
                  <i class="fas fa-envelope me-1"></i> Email
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Kartu Pembimbing (Read Only) --}}
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
          <h6 class="fw-bold m-0"><i class="fas fa-user-tie me-2 text-primary"></i>Pembimbing Lapangan</h6>
        </div>
        <div class="card-body pt-0">
          @if($item->pembimbing)
          <div class="d-flex align-items-center bg-light p-3 rounded">
            <div class="avatar-sm bg-white border rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
              <span class="fw-bold text-primary">{{ substr($item->pembimbing->nama, 0, 1) }}</span>
            </div>
            <div>
              <div class="fw-bold text-dark">{{ $item->pembimbing->nama }}</div>
              <div class="small text-muted">{{ $item->pembimbing->no_hp ?? '-' }}</div>
            </div>
          </div>
          @else
          <div class="alert alert-warning small mb-0">Tidak ada data pembimbing.</div>
          @endif
        </div>
      </div>
    </div>

    {{-- KOLOM KANAN: Detail Arsip & Dokumen --}}
    <div class="col-lg-8">

      {{-- 1. Informasi PKL --}}
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
          <h6 class="fw-bold m-0 text-primary">Data Pelaksanaan PKL</h6>
          <span class="badge bg-light text-secondary border">Arsip Final</span>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="small text-muted">Posisi PKL</label>
              <div class="fw-bold fs-5">{{ $item->lowongan->posisi }}</div>
            </div>
            <div class="col-md-6">
              <label class="small text-muted">Periode Pelaksanaan</label>
              <div class="d-flex align-items-center mt-1">
                <i class="far fa-calendar-alt me-2 text-secondary"></i>
                <span class="fw-bold">
                  {{ $item->tgl_mulai_resmi->format('d M Y') }} - {{ $item->tgl_selesai_resmi->format('d M Y') }}
                </span>
              </div>
              <small class="text-muted">
                Total Durasi: {{ $item->tgl_mulai_resmi->diffInDays($item->tgl_selesai_resmi) + 1 }} Hari
              </small>
            </div>
          </div>
        </div>
      </div>


      {{-- 3. Jika Status DO / Berhenti --}}
      @if($item->status == 'berhenti')
      <div class="alert alert-danger shadow-sm border-0 d-flex align-items-start">
        <i class="fas fa-exclamation-triangle fa-lg me-3 mt-1"></i>
        <div>
          <h6 class="fw-bold alert-heading">Catatan Pemberhentian</h6>
          <p class="mb-0">{{ $item->catatan_admin ?? 'Tidak ada catatan spesifik.' }}</p>
        </div>
      </div>
      @endif

      {{-- 4. Ringkasan Presensi (Statistik Sederhana) --}}
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
          <h6 class="fw-bold m-0">Rekapitulasi Kehadiran</h6>
        </div>
        <div class="card-body">
          <div class="row text-center">
            @php
            $total = $item->presensi->count();
            $hadir = $item->presensi->where('status', 'hadir')->count();
            $terlambat = $item->presensi->where('status', 'terlambat')->count();
            $izin = $item->presensi->whereIn('status', ['izin','sakit'])->count();
            $alpha = $item->presensi->where('status', 'alpha')->count();
            @endphp
            <div class="row text-center">
              <div class="col-3 border-end">
                <h4 class="fw-bold text-success mb-0">{{ $hadir }}</h4>
                <small class="text-muted">Hadir</small>
              </div>
              <div class="col-3 border-end">
                <h4 class="fw-bold text-info mb-0">{{ $terlambat }}</h4>
                <small class="text-muted">Terlambat</small>
              </div>
              <div class="col-3 border-end">
                <h4 class="fw-bold text-warning mb-0">{{ $izin }}</h4>
                <small class="text-muted">Izin/Sakit</small>
              </div>
              <div class="col-3">
                <h4 class="fw-bold text-danger mb-0">{{ $alpha }}</h4>
                <small class="text-muted">Alpha</small>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

  </div>
</div>
</div>
@endsection