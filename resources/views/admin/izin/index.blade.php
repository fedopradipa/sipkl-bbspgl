@extends('layouts.app')

@section('content')
<div class="container-fluid">

  <h4 class="fw-bold text-primary mb-4">Verifikasi Izin & Sakit</h4>

  {{-- Filter Status --}}
  <div class="card shadow-sm mb-4 border-0">
    <div class="card-body py-2">
      <div class="btn-group">
        @foreach(['pending', 'disetujui', 'ditolak', 'all'] as $st)
        <a href="{{ route('izin.index.admin', ['status' => $st]) }}"
          class="btn btn-sm {{ $currentStatus == $st ? 'btn-primary' : 'btn-outline-secondary' }} text-capitalize">
          {{ $st }}
        </a>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Tabel Data --}}
  <div class="card shadow mb-4">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th class="ps-4">Peserta</th>
              <th>Jenis & Tanggal</th>
              <th>Alasan</th>
              <th>Bukti</th>
              <th>Status</th>
              <th class="text-end pe-4">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($dataIzin as $item)
            <tr>
              <td class="ps-4">
                <div class="fw-bold">{{ $item->lamaran->pengguna->name }}</div>
                <small class="text-muted">{{ $item->lamaran->lowongan->divisi }}</small>
              </td>
              <td>
                @if($item->jenis_izin == 'sakit')
                <span class="badge bg-info text-white mb-1">Sakit</span>
                @else
                <span class="badge bg-warning text-dark mb-1">Izin</span>
                @endif
                <div class="small text-dark fw-bold">
                  {{ $item->tgl_mulai->format('d M') }} - {{ $item->tgl_selesai->format('d M Y') }}
                </div>
                <small class="text-muted">({{ $item->durasi_hari }} Hari)</small>
              </td>
              <td>
                <span class="text-truncate d-block" style="max-width: 200px;" title="{{ $item->alasan }}">
                  {{ $item->alasan }}
                </span>
              </td>
              <td>
                @if($item->bukti_foto)
                <a href="{{ asset('storage/'.$item->bukti_foto) }}" target="_blank" class="btn btn-sm btn-light border">
                  <i class="fas fa-image text-primary"></i> Lihat
                </a>
                @else
                <span class="text-muted small">-</span>
                @endif
              </td>
              <td>
                @if($item->status == 'pending')
                <span class="badge bg-warning text-dark">Menunggu</span>
                @elseif($item->status == 'disetujui')
                <span class="badge bg-success">Disetujui</span>
                @else
                <span class="badge bg-danger">Ditolak</span>
                @endif
              </td>
              <td class="text-end pe-4">
                @if($item->status == 'pending')
                <div class="btn-group">
                  {{-- Tombol Approve (Langsung Submit) --}}
                  <form action="{{ route('izin.approve.admin', $item->id) }}" method="POST" onsubmit="return confirm('Setujui izin ini? Sistem akan otomatis mengisi absen peserta.');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success rounded-start">
                      <i class="fas fa-check"></i> Terima
                    </button>
                  </form>

                  {{-- Tombol Reject (Modal) --}}
                  <button type="button" class="btn btn-sm btn-danger rounded-end"
                    data-bs-toggle="modal"
                    data-bs-target="#rejectIzinModal"
                    data-id="{{ $item->id }}">
                    <i class="fas fa-times"></i> Tolak
                  </button>
                </div>
                @else
                <span class="text-muted small fst-italic">Selesai</span>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center py-5 text-muted">Tidak ada data pengajuan.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer bg-white">
      {{ $dataIzin->withQueryString()->links() }}
    </div>
  </div>

</div>

{{-- MODAL TOLAK --}}
<div class="modal fade" id="rejectIzinModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="formRejectIzin" method="POST" class="modal-content">
      @csrf
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Tolak Pengajuan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
          <textarea name="catatan_admin" class="form-control" rows="3" required placeholder="Contoh: Bukti surat dokter tidak valid..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger">Tolak Izin</button>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const rejectModal = document.getElementById('rejectIzinModal');
    rejectModal.addEventListener('show.bs.modal', function(event) {
      const btn = event.relatedTarget;
      const id = btn.getAttribute('data-id');
      // Set action form
      document.getElementById('formRejectIzin').action = '/izin/' + id + '/reject';
    });
  });
</script>

@endsection