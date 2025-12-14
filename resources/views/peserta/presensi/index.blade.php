@extends('layouts.app')

@section('content')
<div class="container py-4">

  {{-- Header --}}


  {{-- 1. CARD STATISTIK (Ringkasan) - Grid tetap sama --}}
  <div class="row g-2 mb-4">
    {{-- ... (Kode Card Statistik Anda yang lama sudah bagus, biarkan saja) ... --}}
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm bg-primary text-white h-100">
        <div class="card-body p-3 text-center">
          <h3 class="fw-bold mb-0">{{ $summary['hadir'] }}</h3>
          <small class="opacity-75">Hadir</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm bg-warning text-dark h-100">
        <div class="card-body p-3 text-center">
          <h3 class="fw-bold mb-0">{{ $summary['terlambat'] }}</h3>
          <small class="opacity-75">Terlambat</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm bg-info text-white h-100">
        <div class="card-body p-3 text-center">
          <h3 class="fw-bold mb-0">{{ $summary['izin_sakit'] }}</h3>
          <small class="opacity-75">Sakit/Izin</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm bg-danger text-white h-100">
        <div class="card-body p-3 text-center">
          <h3 class="fw-bold mb-0">{{ $summary['alpha'] }}</h3>
          <small class="opacity-75">Alpha</small>
        </div>
      </div>
    </div>
  </div>

  {{-- 2. FILTER TANGGAL (Dropdown Collapse di HP biar hemat tempat) --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-0 py-3" data-bs-toggle="collapse" href="#filterCollapse" role="button" aria-expanded="false" aria-controls="filterCollapse">
      <div class="d-flex justify-content-between align-items-center">
        <span class="fw-bold text-secondary"><i class="fas fa-filter me-2"></i>Filter Periode</span>
        <i class="fas fa-chevron-down text-muted"></i>
      </div>
    </div>
    <div class="collapse {{ request('mulai') ? 'show' : '' }}" id="filterCollapse">
      <div class="card-body pt-0">
        <form action="{{ route('presensi.riwayat') }}" method="GET" class="row g-2">
          <div class="col-6">
            <label class="small text-muted">Dari</label>
            <input type="date" name="mulai" class="form-control form-control-sm" value="{{ $mulai }}">
          </div>
          <div class="col-6">
            <label class="small text-muted">Sampai</label>
            <input type="date" name="sampai" class="form-control form-control-sm" value="{{ $sampai }}">
          </div>
          <div class="col-12 mt-3">
            <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">Tampilkan Data</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- 3. TABEL DATA (OPTIMIZED FOR MOBILE) --}}
  <div class="card border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 mobile-table">
        <thead class="bg-light">
          <tr>
            <th class="ps-3" width="30%">Tanggal</th>
            <th width="40%">Jam Kerja</th>
            <th class="text-end pe-3" width="30%">Status</th>
          </tr>
        </thead>
        <tbody class="border-top-0">
          @forelse($presensi as $item)
          <tr>
            {{-- KOLOM 1: TANGGAL (Stacked) --}}
            <td class="ps-3">
              <div class="d-flex flex-column">
                <span class="fw-bold text-dark" style="font-size: 0.9rem;">
                  {{ $item->tanggal->format('d M') }}
                </span>
                <span class="text-muted small" style="font-size: 0.75rem;">
                  {{ $item->tanggal->translatedFormat('D, Y') }}
                </span>
              </div>
            </td>

            {{-- KOLOM 2: WAKTU (Masuk & Pulang digabung) --}}
            <td>
              <div class="d-flex flex-column" style="font-size: 0.85rem;">
                {{-- Masuk --}}
                <div class="mb-1">
                  <i class="fas fa-arrow-right text-success me-1" style="font-size: 0.7rem;"></i>
                  <span class="fw-bold {{ $item->status == 'terlambat' ? 'text-danger' : 'text-dark' }}">
                    {{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}
                  </span>
                  @if($item->status == 'terlambat')
                  <i class="fas fa-exclamation-circle text-danger ms-1" style="font-size: 0.7rem;" title="Terlambat"></i>
                  @endif
                </div>

                {{-- Pulang --}}
                <div>
                  <i class="fas fa-arrow-left text-danger me-1" style="font-size: 0.7rem;"></i>
                  @if($item->jam_pulang)
                  <span class="text-secondary">
                    {{ \Carbon\Carbon::parse($item->jam_pulang)->format('H:i') }}
                  </span>
                  @else
                  <span class="text-muted fst-italic" style="font-size: 0.75rem;">--:--</span>
                  @endif
                </div>
              </div>
            </td>

            {{-- KOLOM 3: STATUS (Badge) --}}
            <td class="text-end pe-3">
              @if($item->status == 'hadir')
              <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill" style="font-size: 0.7rem;">
                Hadir
              </span>
              @elseif($item->status == 'sakit')
              <span class="badge bg-info bg-opacity-10 text-info px-2 py-1 rounded-pill" style="font-size: 0.7rem;">
                Sakit
              </span>
              @elseif($item->status == 'izin')
              <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1 rounded-pill" style="font-size: 0.7rem;">
                Izin
              </span>
              @else
              <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-pill" style="font-size: 0.7rem;">
                Alpha
              </span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="3" class="text-center py-5">
              <div class="text-muted opacity-50 mb-2">
                <i class="fas fa-calendar-times fa-2x"></i>
              </div>
              <small class="text-muted">Tidak ada data.</small>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    @if($presensi->hasPages())
    <div class="card-footer bg-white py-3">
      {{ $presensi->links('pagination::simple-bootstrap-5') }}
      {{-- Gunakan simple pagination biar gak makan tempat di HP --}}
    </div>
    @endif
  </div>

</div>

{{-- CSS KHUSUS BIAR RAPI DI HP --}}
<style>
  /* Biar tabel tidak terlalu rapat tapi muat di layar kecil */
  .mobile-table td {
    padding-top: 12px;
    padding-bottom: 12px;
    vertical-align: middle;
  }

  /* Perkecil font badge */
  .badge {
    font-weight: 600;
    letter-spacing: 0.5px;
  }
</style>
@endsection