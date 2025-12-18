<div class="card border-0 shadow-sm overflow-hidden">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0 mobile-table">
      <thead class="bg-light">
        <tr>
          <th class="ps-3 py-3" width="35%">Tanggal</th>
          <th width="35%">Waktu</th>
          <th class="text-end pe-3" width="30%">Status</th>
        </tr>
      </thead>
      <tbody class="border-top-0">
        @forelse($presensi as $item)
        <tr>
          {{-- KOLOM 1: TANGGAL --}}
          <td class="ps-3">
            <div class="d-flex flex-column">
              <span class="fw-bold text-dark" style="font-size: 0.9rem;">
                {{ $item->tanggal->format('d M') }}
              </span>
              <span class="text-muted small" style="font-size: 0.75rem;">
                {{ $item->tanggal->translatedFormat('l, Y') }}
              </span>
            </div>
          </td>

          {{-- KOLOM 2: WAKTU (FIX BUG NULL) --}}
          <td>
            <div class="d-flex flex-column" style="font-size: 0.85rem;">
              {{-- Jam Masuk --}}
              <div class="mb-1">
                @if($item->jam_masuk)
                <i class="fas fa-arrow-right text-success me-1" style="font-size: 0.7rem;"></i>
                <span class="fw-bold {{ $item->status == 'terlambat' ? 'text-danger' : 'text-dark' }}">
                  {{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}
                </span>
                @if($item->status == 'terlambat')
                <i class="fas fa-exclamation-circle text-danger ms-1" style="font-size: 0.7rem;"></i>
                @endif
                @else
                {{-- Jika Kosong (Sakit/Izin/Alpha) --}}
                <span class="text-muted small fst-italic">-</span>
                @endif
              </div>

              {{-- Jam Pulang --}}
              <div>
                @if($item->jam_pulang)
                <i class="fas fa-arrow-left text-danger me-1" style="font-size: 0.7rem;"></i>
                <span class="text-secondary">
                  {{ \Carbon\Carbon::parse($item->jam_pulang)->format('H:i') }}
                </span>
                @else
                {{-- Jika Masuk ada tapi Pulang belum --}}
                @if($item->jam_masuk)
                <span class="badge bg-warning text-dark" style="font-size: 0.6rem;">Belum</span>
                @else
                <span class="text-muted small fst-italic">-</span>
                @endif
                @endif
              </div>
            </div>
          </td>

          {{-- KOLOM 3: STATUS (Badge) --}}
          <td class="text-end pe-3">
            @php
            $colors = [
            'hadir' => 'success',
            'terlambat' => 'warning',
            'sakit' => 'info',
            'izin' => 'primary',
            'alpha' => 'danger'
            ];
            $color = $colors[$item->status] ?? 'secondary';
            @endphp
            <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} px-2 py-1 rounded-pill text-uppercase" style="font-size: 0.65rem;">
              {{ $item->status }}
            </span>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="3" class="text-center py-5">
            <div class="text-muted opacity-50 mb-2">
              <i class="fas fa-calendar-times fa-2x"></i>
            </div>
            <small class="text-muted">Belum ada data riwayat.</small>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination Simple (Cocok untuk HP) --}}
  @if($presensi->hasPages())
  <div class="card-footer bg-white py-3">
    {{ $presensi->links('pagination::simple-bootstrap-5') }}
  </div>
  @endif
</div>

<style>
  .mobile-table td {
    padding-top: 10px;
    padding-bottom: 10px;
    vertical-align: middle;
  }
</style>