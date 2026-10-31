<div class="table-responsive">
  <table class="table table-hover align-middle mb-0">
    <thead class="bg-light">
      <tr>
        <th class="ps-4 py-3" width="5%"><span class="text-muted small fw-bold">#</span></th>
        <th width="22%"><span class="text-muted small fw-bold text-uppercase">Peserta</span></th>
        <th width="14%"><span class="text-muted small fw-bold text-uppercase"><i class="fas fa-sign-in-alt me-1"></i>Masuk</span></th>
        <th width="12%"><span class="text-muted small fw-bold text-uppercase">Status</span></th>
        <th width="32%"><span class="text-muted small fw-bold text-uppercase"><i class="fas fa-comment-alt me-1"></i>Catatan</span></th>
        <th class="text-end pe-4" width="10%"><span class="text-muted small fw-bold text-uppercase">Aksi</span></th>
      </tr>
    </thead>

    <tbody>
      {{-- CONFIG WARNA STATUS --}}
      @php
      $statusConfig = [
      'hadir' => ['color' => 'success', 'icon' => 'check-circle', 'text' => 'Hadir'],
      'terlambat' => ['color' => 'warning', 'icon' => 'exclamation-triangle', 'text' => 'Terlambat'],
      'sakit' => ['color' => 'info', 'icon' => 'medkit', 'text' => 'Sakit'],
      'izin' => ['color' => 'primary', 'icon' => 'file-alt', 'text' => 'Izin'],
      'alpha' => ['color' => 'danger', 'icon' => 'times-circle', 'text' => 'Alpha'],
      'belum_absen' => ['color' => 'secondary', 'icon' => 'hourglass-half', 'text' => 'Belum Absen'],
      ];
      @endphp

      @forelse($presensis as $index => $item)
      <tr class="attendance-row">
        {{-- 1. NOMOR URUT --}}
        <td class="ps-4">
          <span class="text-muted fw-semibold">
            @if($presensis instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $presensis->firstItem() + $index }}
            @else
            {{ $loop->iteration }}
            @endif
          </span>
        </td>

        {{-- 2. PESERTA --}}
        <td>
          <div class="d-flex align-items-center">
            @php
            $user = $item->lamaran->pengguna;
            $profilePic = $user->profile->foto_profil ?? null;
            $avatar = $profilePic
            ? asset('storage/foto_profil/' . $profilePic)
            : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&color=fff&size=40';
            @endphp

            <img src="{{ $avatar }}" class="rounded-circle me-3 shadow-sm" width="35" height="35" style="object-fit: cover;">
            <div>
              <div class="fw-bold text-dark small">{{ $user->name }}</div>
              <div class="text-muted" style="font-size: 0.75rem;">
                {{ Str::limit($item->lamaran->lowongan->posisi, 20) }}
              </div>
            </div>
          </div>
        </td>

        {{-- 3. JAM MASUK --}}
        <td>
          @if($item->jam_masuk)
          <div class="d-flex flex-column">
            <span class="fw-bold small {{ $item->status == 'terlambat' ? 'text-danger' : 'text-success' }}">
              {{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}
            </span>
            @if($item->status == 'terlambat')
            <span class="badge bg-danger bg-opacity-10 text-danger px-1" style="font-size: 0.6rem; width: fit-content;">LATE</span>
            @endif
          </div>
          @else
          <span class="text-muted small">-</span>
          @endif
        </td>

        {{-- 4. STATUS --}}
        <td>
          @php
          $config = $statusConfig[$item->status] ?? ['color' => 'secondary', 'icon' => 'circle', 'text' => ucfirst($item->status)];
          @endphp
          <span class="badge bg-{{ $config['color'] }} bg-opacity-10 text-{{ $config['color'] }} border border-{{ $config['color'] }} px-2 py-1 rounded-pill" style="font-size: 0.75rem;">
            {{ $config['text'] }}
          </span>
        </td>

        {{-- 5. CATATAN DENGAN HOVER TOOLTIP --}}
        <td>
          @if($item->catatan_harian && $item->catatan_harian != '-')
          <div class="d-flex align-items-center"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="{{ $item->catatan_harian }}"
            style="cursor: help;">

            <div class="text-muted small text-truncate" style="max-width: 150px; font-size: 0.8rem;">
              {{ $item->catatan_harian }}
            </div>
          </div>
          @else
          <span class="text-muted small fst-italic" style="font-size: 0.8rem;">-</span>
          @endif
        </td>

        {{-- 6. AKSI --}}
        <td class="text-end pe-4">
          @if($item->id)
          <div class="btn-group shadow-sm" role="group">
            <button type="button" class="btn btn-sm btn-light border"
              data-bs-toggle="modal"
              data-bs-target="#editModal"
              data-id="{{ $item->id }}"
              data-status="{{ $item->status }}"
              data-masuk="{{ $item->jam_masuk }}"
              data-catatan="{{ $item->catatan_harian }}"
              title="Edit Data">
              <i class="fas fa-pen text-warning" style="font-size: 0.8rem;"></i>
            </button>
          </div>

          <form id="delete-form-{{ $item->id }}" action="{{ route('admin.presensi.destroy', $item->id) }}" method="POST" class="d-none">
            @csrf @method('DELETE')
          </form>
          @else
          <button class="btn btn-sm btn-outline-primary shadow-sm py-1 px-2" style="font-size: 0.8rem;"
            data-bs-toggle="modal"
            data-bs-target="#createModal"
            data-lamaran-id="{{ $item->lamaran->id }}"
            data-nama="{{ $user->name }}"
            title="Input Kehadiran">
            <i class="fas fa-plus-circle me-1"></i> Input
          </button>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="6" class="text-center py-5">
          <div class="opacity-50">
            <i class="fas fa-clipboard-check fa-3x mb-3 text-muted"></i>
            <p class="text-muted mb-0">Tidak ada data presensi ditampilkan.</p>
          </div>
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- PAGINATION --}}
@if($presensis instanceof \Illuminate\Pagination\LengthAwarePaginator)
<div class="d-flex justify-content-end px-4 py-3 border-top">
  {{ $presensis->links() }}
</div>
@endif

{{-- JAVASCRIPT ONLY FOR DELETE --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  });

  function confirmDelete(id, name) {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        text: `Hapus presensi ${name}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById('delete-form-' + id).submit();
        }
      });
    } else {
      if (confirm(`Hapus presensi ${name}?`)) {
        document.getElementById('delete-form-' + id).submit();
      }
    }
  }
</script>