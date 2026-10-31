@extends('layouts.app')

@section('content')
<div class="container-fluid">

  <div class="card shadow-sm mb-4 border-0 rounded-3">
    <div class="card-body py-3">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">

        <div class="d-flex align-items-center gap-2">
          <div class="btn-group shadow-sm">
            @foreach(['pending', 'disetujui', 'ditolak'] as $st)
            <a href="{{ route('admin.izin.index', ['status' => $st, 'search' => request('search')]) }}"
              class="btn btn-sm {{ $currentStatus == $st ? 'btn-primary' : 'btn-white border' }} text-capitalize px-3">
              {{ $st }}
            </a>
            @endforeach
          </div>
        </div>

        <form action="{{ route('admin.izin.index') }}" method="GET">
          <input type="hidden" name="status" value="{{ request('status', 'pending') }}">

          <div class="input-group input-group-sm shadow-sm" style="width: 250px;">
            <input type="text" name="search" class="form-control border-end-0"
              placeholder="Cari nama peserta..." value="{{ request('search') }}">

            <button class="btn btn-primary" type="submit" title="Cari">
              <i class="fas fa-search"></i>
            </button>

            @if(request('search'))
            <a href="{{ route('admin.izin.index', ['status' => request('status', 'pending')]) }}"
              class="btn btn-outline-secondary border-start-0 bg-white" title="Reset">
              <i class="fas fa-times"></i>
            </a>
            @endif
          </div>
        </form>

      </div>
    </div>
  </div>

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
              @if($currentStatus == 'pending')
              <th class="text-end pe-4">Aksi</th>
              @endif
            </tr>
          </thead>

          <tbody>
            @forelse($dataIzin as $item)
            @php
            $dates = $item->tanggal_izin ?? [];
            sort($dates);
            $firstDate = count($dates) > 0 ? \Carbon\Carbon::parse($dates[0]) : null;
            $lastDate = count($dates) > 0 ? \Carbon\Carbon::parse(end($dates)) : null;
            $totalHari = count($dates);

            // ==== FOTO PROFIL (AMAN) ====
            $user = $item->lamaran->pengguna;
            $filename = optional($user->profile)->foto_profil;

            if ($filename) {
            $path = str_contains($filename, '/') ? $filename : 'foto_profil/'.$filename;
            $fotoUrl = asset('storage/'.$path);
            } else {
            $fotoUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&color=fff&size=40';
            }

            $fallback = 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&color=fff&size=40';
            @endphp

            <tr>
              {{-- KOLOM PESERTA + FOTO --}}
              <td class="ps-4">
                <div class="d-flex align-items-center">
                  <img src="{{ $fotoUrl }}"
                    class="rounded-circle me-3 shadow-sm border"
                    width="40" height="40"
                    style="object-fit: cover;"
                    alt="Foto {{ $user->name }}"
                    onerror="this.onerror=null; this.src='{{ $fallback }}';">

                  <div>
                    <div class="fw-bold text-dark">{{ $user->name }}</div>
                    <small class="text-muted">{{ $item->lamaran->lowongan->posisi }}</small>
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

              <td style="max-width: 250px;">
                <span class="d-inline-block text-truncate w-100 cursor-pointer"
                  data-bs-toggle="tooltip"
                  data-bs-placement="top"
                  title="{{ $item->alasan }}">
                  {{ $item->alasan }}
                </span>
              </td>

              <td>
                @if($item->bukti_lampiran)
                <a href="{{ asset('storage/'.$item->bukti_lampiran) }}" target="_blank" class="btn btn-sm btn-light border text-primary">
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

              @if($currentStatus == 'pending')
              <td class="text-end pe-4">
                <div class="btn-group gap-1">
                  <button type="button" class="btn btn-sm btn-success rounded"
                    data-bs-toggle="modal"
                    data-bs-target="#approveIzinModal"
                    data-url="{{ route('admin.izin.approve', $item->id) }}"
                    title="Terima">
                    <i class="fas fa-check"></i>
                  </button>

                  <button type="button" class="btn btn-sm btn-danger rounded"
                    data-bs-toggle="modal"
                    data-bs-target="#rejectIzinModal"
                    data-url="{{ route('admin.izin.reject', $item->id) }}"
                    title="Tolak">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </td>
              @endif
            </tr>

            @empty
            <tr>
              <td colspan="{{ $currentStatus == 'pending' ? 6 : 5 }}" class="text-center py-5">
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

@include('admin.izin.partials.modals')
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function(el) {
      return new bootstrap.Tooltip(el)
    })

    const approveModal = document.getElementById('approveIzinModal');
    if (approveModal) {
      approveModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const url = button.getAttribute('data-url');
        document.getElementById('formApproveIzin').action = url;
      });
    }

    const rejectModal = document.getElementById('rejectIzinModal');
    if (rejectModal) {
      rejectModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const url = button.getAttribute('data-url');
        document.getElementById('formRejectIzin').action = url;
        document.getElementById('catatan_admin_izin').value = '';
      });
    }
  });
</script>
@endpush