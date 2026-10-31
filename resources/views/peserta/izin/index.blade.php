@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Riwayat Izin</h4>
    <a href="{{ route('izin.create') }}" class="btn btn-primary btn-sm rounded-pill px-3">
      <i class="fas fa-plus me-1"></i> Ajukan Baru
    </a>
  </div>

  <div class="row">
    @forelse($izin as $item)

    {{-- LOGIKA PENGOLAHAN TANGGAL ARRAY --}}
    @php
    $dates = $item->tanggal_izin ?? [];
    sort($dates);
    $totalHari = count($dates);
    $firstDate = $totalHari > 0 ? \Carbon\Carbon::parse($dates[0]) : null;
    $lastDate = $totalHari > 0 ? \Carbon\Carbon::parse($dates[$totalHari - 1]) : null;
    @endphp

    <div class="col-md-6 mb-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          {{-- Header Card: Jenis Izin & Waktu dibuat --}}
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
              @if($item->jenis_izin == 'sakit')
              <span class="badge bg-info text-white"><i class="fas fa-briefcase-medical me-1"></i> Sakit</span>
              @else
              <span class="badge bg-warning text-dark"><i class="fas fa-info-circle me-1"></i> Izin</span>
              @endif
            </div>
            <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
          </div>

          {{-- TAMPILAN TANGGAL --}}
          <h6 class="fw-bold mt-2">
            @if($totalHari > 1)
            {{ $firstDate->translatedFormat('d M') }} - {{ $lastDate->translatedFormat('d M Y') }}
            @elseif($totalHari == 1)
            {{ $firstDate->translatedFormat('d M Y') }}
            @else
            <span class="text-danger">Tanggal Error</span>
            @endif
            <span class="text-muted small fw-normal">({{ $totalHari }} Hari)</span>
          </h6>

          {{-- List Tanggal Kecil --}}
          @if($totalHari > 0)
          <div class="mb-3">
            @foreach($dates as $d)
            <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.7rem">
              {{ \Carbon\Carbon::parse($d)->translatedFormat('d M') }}
            </span>
            @endforeach
          </div>
          @endif

          <p class="text-muted small mb-3 text-truncate">{{ $item->alasan }}</p>

          {{-- FOOTER CARD: STATUS & ACTIONS --}}
          <div class="d-flex justify-content-between align-items-center border-top pt-3">

            {{-- KIRI: Status & Tombol Edit --}}
            <div class="d-flex align-items-center gap-2">
              @if($item->status == 'pending')
              <span class="badge bg-secondary bg-opacity-10 text-secondary px-3">Menunggu</span>

              {{-- TOMBOL EDIT (Hanya muncul jika pending) --}}
              <a href="{{ route('izin.edit', $item->id) }}" class="btn btn-sm btn-outline-warning" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; font-size: .75rem;">
                <i class="fas fa-pencil-alt me-1"></i> Edit
              </a>

              @elseif($item->status == 'disetujui')
              <span class="badge bg-success bg-opacity-10 text-success px-3">Disetujui</span>
              @else
              <span class="badge bg-danger bg-opacity-10 text-danger px-3">Ditolak</span>
              @endif
            </div>

            {{-- KANAN: Tombol Lihat Bukti --}}
            @if($item->bukti_lampiran)
            <a href="{{ asset('storage/'.$item->bukti_lampiran) }}" target="_blank" class="btn btn-sm btn-light text-primary" title="Lihat Bukti">
              <i class="fas fa-paperclip"></i>
            </a>
            @endif
          </div>

          {{-- Catatan Admin (Jika Ditolak) --}}
          @if($item->status == 'ditolak' && $item->catatan_admin)
          <div class="alert alert-danger small mt-3 mb-0 p-2">
            <strong>Admin:</strong> {{ $item->catatan_admin }}
          </div>
          @endif
        </div>
      </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
      <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Page%20with%20Curl.png" width="80" class="mb-3">
      <p class="text-muted">Belum ada riwayat pengajuan izin.</p>
      <a href="{{ route('izin.create') }}" class="btn btn-outline-primary btn-sm">Buat Pengajuan Sekarang</a>
    </div>
    @endforelse
  </div>

  <div class="mt-4">
    {{ $izin->links() }}
  </div>
</div>
@endsection