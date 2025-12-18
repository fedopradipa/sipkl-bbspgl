@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold text-primary mb-1">Arsip Alumni Magang</h4>
      <p class="text-muted small mb-0">Database riwayat peserta yang telah menyelesaikan program.</p>
    </div>
    {{-- TOMBOL PDF SUDAH DIHAPUS DARI SINI --}}
  </div>

  {{-- Filter Bar --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">

    {{-- Filter Status (Kiri) --}}
    <div class="btn-group shadow-sm">
      <a href="{{ route('admin.alumni.index', ['status' => 'all']) }}"
        class="btn btn-sm px-3 {{ $currentStatus == 'all' ? 'btn-primary' : 'btn-white border' }}">
        Semua
      </a>
      <a href="{{ route('admin.alumni.index', ['status' => 'selesai']) }}"
        class="btn btn-sm px-3 {{ $currentStatus == 'selesai' ? 'btn-primary' : 'btn-white border' }}">
        <i class="fas fa-user-graduate me-1"></i> Lulus
      </a>
      <a href="{{ route('admin.alumni.index', ['status' => 'berhenti']) }}"
        class="btn btn-sm px-3 {{ $currentStatus == 'berhenti' ? 'btn-primary' : 'btn-white border' }}">
        <i class="fas fa-user-times me-1"></i> Drop Out
      </a>
    </div>

    {{-- Kanan: Wrapper Search & PDF (Sejajar) --}}
    <div class="d-flex gap-2">

      {{-- Search Bar --}}
      <form action="{{ route('admin.alumni.index') }}" method="GET">
        <input type="hidden" name="status" value="{{ $currentStatus }}">
        <div class="input-group input-group-sm shadow-sm" style="width: 250px;">
          <input type="text" name="keyword" class="form-control border-end-0"
            placeholder="Cari nama alumni..." value="{{ request('keyword') }}">

          <button class="btn btn-primary" type="submit" title="Cari">
            <i class="fas fa-search"></i>
          </button>

          @if(request('keyword'))
          <a href="{{ route('admin.alumni.index', ['status' => $currentStatus]) }}" class="btn btn-outline-secondary border-start-0 bg-white" title="Reset">
            <i class="fas fa-times"></i>
          </a>
          @endif
        </div>
      </form>

      {{-- TOMBOL PDF (DIPINDAHKAN KESINI) --}}
      <a href="{{ route('admin.alumni.export', request()->all()) }}" class="btn btn-danger btn-sm shadow-sm fw-bold d-flex align-items-center">
        <i class="fas fa-file-pdf me-2"></i> PDF
      </a>

    </div>
  </div>

  {{-- Tabel Data (TIDAK ADA PERUBAHAN) --}}
  <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-secondary">
          <tr>
            <th class="ps-4 py-3 text-uppercase small fw-bold">Alumni</th>
            <th class="text-uppercase small fw-bold">Riwayat Magang</th>
            <th class="text-uppercase small fw-bold">Status Akhir</th>
            <th class="text-end pe-4 text-uppercase small fw-bold">File Laporan</th>
          </tr>
        </thead>
        <tbody class="border-top-0">
          @forelse($alumni as $item)
          <tr>
            {{-- 1. Profil --}}
            <td class="ps-4 py-3">
              <div class="d-flex align-items-center">
                @php
                $foto = $item->pengguna->profile->foto_profil ?? null;
                $avatar = $foto ? asset('storage/foto_profil/'.$foto) : 'https://ui-avatars.com/api/?name='.urlencode($item->pengguna->name).'&background=random&color=fff';
                @endphp
                <img src="{{ $avatar }}" class="rounded-circle me-3 border shadow-sm" width="40" height="40" style="object-fit: cover;">
                <div>
                  <div class="fw-bold text-dark">{{ $item->pengguna->name }}</div>

                  {{-- Tombol Detail Kecil di bawah nama --}}
                  <a href="{{ route('admin.alumni.show', $item->id) }}" class="small text-primary text-decoration-none">
                    Lihat Detail <i class="fas fa-arrow-right ms-1" style="font-size: 0.7em;"></i>
                  </a>
                </div>
              </div>
            </td>

            {{-- 2. Riwayat --}}
            <td>
              <div class="fw-bold text-dark small mb-1">{{ $item->lowongan->judul }}</div>
              <div class="d-flex align-items-center small text-muted">
                <i class="far fa-calendar-alt me-1"></i>
                {{ $item->tgl_mulai_resmi->format('M Y') }} -
                {{ $item->tgl_selesai_resmi->format('M Y') }}
              </div>
            </td>

            {{-- 3. Status --}}
            <td>
              @if($item->status == 'selesai')
              <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1">
                <i class="fas fa-check-circle me-1"></i> LULUS
              </span>
              @else
              <div class="d-flex align-items-center gap-2">
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1">
                  <i class="fas fa-ban me-1"></i> DO
                </span>
                @if($item->catatan_admin)
                <span class="text-muted cursor-pointer"
                  data-bs-toggle="tooltip"
                  title="Alasan: {{ $item->catatan_admin }}">
                  <i class="fas fa-info-circle"></i>
                </span>
                @endif
              </div>
              @endif
            </td>

            {{-- 4. Arsip Laporan --}}
            <td class="text-end pe-4">
              @if($item->path_laporan_akhir)
              <a href="{{ asset('storage/laporan_akhir/'.$item->path_laporan_akhir) }}"
                target="_blank"
                class="btn btn-sm btn-outline-danger shadow-sm border-danger"
                title="Download Laporan Akhir (PDF)">
                <i class="fas fa-file-pdf me-1"></i> Laporan Akhir
              </a>
              @else
              <span class="text-muted small fst-italic">Tidak ada file</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="4" class="text-center py-5">
              <div class="mb-2 opacity-25">
                <i class="fas fa-archive fa-3x"></i>
              </div>
              <h6 class="text-muted fw-bold">Belum ada arsip alumni.</h6>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($alumni->hasPages())
    <div class="card-footer bg-white border-top-0 py-3">
      {{ $alumni->links() }}
    </div>
    @endif
  </div>
</div>

<script>
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  })
</script>
@endsection