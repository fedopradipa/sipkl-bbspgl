@extends('layouts.app')

@section('content')
<div class="container-fluid">

  <div class="card shadow-sm mb-4 border-0 rounded-3">
    <div class="card-body py-3">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">

        {{-- 1. Filter Status (Updated: Group Style Senada Alumni) --}}
        <div class="d-flex align-items-center gap-2">
          <div class="btn-group shadow-sm">
            @foreach(['pending', 'disetujui', 'ditolak', 'all'] as $st)
            <a href="{{ route('admin.izin.index', ['status' => $st, 'search' => request('search')]) }}"
              class="btn btn-sm {{ $currentStatus == $st ? 'btn-primary' : 'btn-white border' }} text-capitalize px-3">
              {{ $st }}
            </a>
            @endforeach
          </div>
        </div>

        {{-- 2. Search Bar (Updated Style) --}}
        <form action="{{ route('admin.izin.index') }}" method="GET">
          <input type="hidden" name="status" value="{{ request('status', 'pending') }}">

          <div class="input-group input-group-sm shadow-sm" style="width: 250px;">
            <input type="text" name="search" class="form-control border-end-0"
              placeholder="Cari nama peserta..." value="{{ request('search') }}">

            <button class="btn btn-primary" type="submit" title="Cari">
              <i class="fas fa-search"></i>
            </button>

            @if(request('search'))
            <a href="{{ route('admin.izin.index', ['status' => request('status', 'pending')]) }}" class="btn btn-outline-secondary border-start-0 bg-white" title="Reset">
              <i class="fas fa-times"></i>
            </a>
            @endif
          </div>
        </form>

      </div>
    </div>
  </div>

  {{-- TABEL DATA --}}
  <div class="card shadow mb-4">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-secondary">
            <tr>
              <th class="ps-4 py-3">Peserta</th>
              <th>Jenis & Tanggal</th>
              <th>Alasan</th>
              <th>Bukti</th>
              <th>Status</th>
              <th class="text-end pe-4">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($dataIzin as $item)

            {{-- LOGIC HITUNG TANGGAL (TETAP SAMA) --}}
            @php
            $dates = $item->tanggal_izin ?? [];
            sort($dates);
            $firstDate = count($dates) > 0 ? \Carbon\Carbon::parse($dates[0]) : null;
            $lastDate = count($dates) > 0 ? \Carbon\Carbon::parse(end($dates)) : null;
            $totalHari = count($dates);
            @endphp
            {{-- END LOGIC --}}

            <tr>
              <td class="ps-4">
                <div class="d-flex align-items-center">
                  {{-- Avatar Initials (UX Tambahan: Biar lebih manis) --}}
                  <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3"
                    style="width: 40px; height: 40px; font-weight:bold;">
                    {{ substr($item->lamaran->pengguna->name, 0, 1) }}
                  </div>
                  <div>
                    <div class="fw-bold text-dark">{{ $item->lamaran->pengguna->name }}</div>
                    <small class="text-muted">{{ $item->lamaran->lowongan->divisi }}</small>
                  </div>
                </div>
              </td>
              <td>
                @if($item->jenis_izin == 'sakit')
                <span class="badge bg-info bg-opacity-10 text-info border border-info mb-1">Sakit</span>
                @else
                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning mb-1">Izin</span>
                @endif

                <div class="small text-dark fw-bold">
                  @if($firstDate)
                  {{ $firstDate->format('d M') }}
                  @if($totalHari > 1)
                  - {{ $lastDate->format('d M Y') }}
                  @else
                  {{ $firstDate->format('Y') }}
                  @endif
                  @else
                  <span class="text-danger">-</span>
                  @endif
                </div>
                <small class="text-muted">({{ $totalHari }} Hari)</small>
              </td>

              {{-- UX UPDATE: TOOLTIP BOOTSTRAP --}}
              <td style="max-width: 250px;">
                <span class="d-inline-block text-truncate w-100 cursor-pointer"
                  data-bs-toggle="tooltip"
                  data-bs-placement="top"
                  title="{{ $item->alasan }}">
                  {{ $item->alasan }}
                </span>
              </td>

              <td>
                @if($item->bukti_foto)
                <a href="{{ asset('storage/'.$item->bukti_foto) }}" target="_blank" class="btn btn-sm btn-light border text-primary">
                  <i class="fas fa-paperclip me-1"></i> Bukti
                </a>
                @else
                <span class="text-muted small fst-italic">-</span>
                @endif
              </td>
              <td>
                @if($item->status == 'pending')
                <span class="badge bg-secondary">Menunggu</span>
                @elseif($item->status == 'disetujui')
                <span class="badge bg-success">Disetujui</span>
                @else
                <span class="badge bg-danger">Ditolak</span>
                @endif
              </td>

              <td class="text-end pe-4">
                @if($item->status == 'pending')
                <div class="btn-group gap-1">
                  {{-- Tombol Approve --}}
                  <button type="button" class="btn btn-sm btn-success rounded"
                    data-bs-toggle="modal"
                    data-bs-target="#approveIzinModal"
                    data-url="{{ route('admin.izin.approve', $item->id) }}"
                    title="Terima">
                    <i class="fas fa-check"></i>
                  </button>

                  {{-- Tombol Reject --}}
                  <button type="button" class="btn btn-sm btn-danger rounded"
                    data-bs-toggle="modal"
                    data-bs-target="#rejectIzinModal"
                    data-url="{{ route('admin.izin.reject', $item->id) }}"
                    title="Tolak">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
                @else
                @if($item->status == 'disetujui')
                <i class="fas fa-check-circle text-success fs-5" title="Disetujui"></i>
                @else
                <i class="fas fa-times-circle text-danger fs-5" title="Ditolak"></i>
                @endif
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center py-5">
                <div class="text-muted mb-2"><i class="fas fa-inbox fa-3x opacity-25"></i></div>
                <p class="text-muted">Tidak ada data pengajuan ditemukan.</p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer bg-white border-top-0 py-3">
      {{ $dataIzin->withQueryString()->links() }}
    </div>
  </div>

</div>

{{-- INCLUDE PARTIAL MODALS --}}
@include('admin.izin.partials.modals')

@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {

    // 1. INISIALISASI BOOTSTRAP TOOLTIP (UX BEST PRACTICE)
    // Ini membuat title="..." muncul sebagai popup hitam cantik
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    // 2. LOGIC MODAL APPROVE
    const approveModal = document.getElementById('approveIzinModal');
    if (approveModal) {
      approveModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const url = button.getAttribute('data-url');
        const form = document.getElementById('formApproveIzin');
        form.action = url;
      });
    }

    // 3. LOGIC MODAL REJECT
    const rejectModal = document.getElementById('rejectIzinModal');
    if (rejectModal) {
      rejectModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const url = button.getAttribute('data-url');
        const form = document.getElementById('formRejectIzin');
        form.action = url;
        document.getElementById('catatan_admin_izin').value = '';
      });
    }
  });
</script>
@endpush