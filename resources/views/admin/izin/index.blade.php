@extends('layouts.app')

@section('content')
<div class="container-fluid">
  {{-- Filter Status (TAMPILAN TETAP SAMA) --}}
  <div class="card shadow-sm mb-4 border-0">
    <div class="card-body py-2">
      <div class="btn-group">
        @foreach(['pending', 'disetujui', 'ditolak', 'all'] as $st)
        <a href="{{ route('admin.izin.index', ['status' => $st]) }}"
          class="btn btn-sm {{ $currentStatus == $st ? 'btn-primary' : 'btn-outline-secondary' }} text-capitalize">
          {{ $st }}
        </a>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Tabel Data (TAMPILAN TETAP SAMA) --}}
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
                <div class="btn-group gap-1">
                  {{-- Tombol Approve --}}
                  <form action="{{ route('izin.approve.admin', $item->id) }}" method="POST" onsubmit="return confirm('Setujui izin ini? Sistem akan otomatis mengisi absen peserta.');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success rounded-start" title="Terima">
                      <i class="fas fa-check"></i>
                    </button>
                  </form>

                  {{-- Tombol Reject (FIXED) --}}
                  {{-- Kita kirim data-url lengkap dari Laravel, bukan rakit di JS --}}
                  <button type="button" class="btn btn-warning btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#rejectIzinModal"
                    data-url="{{ route('izin.reject.admin', $item->id) }}"
                    title="Tolak">
                    <i class="fas fa-times"></i>
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

{{-- INCLUDE MODAL DARI FOLDER PARTIALS --}}
@include('admin.izin.partials.reject_modal')

@endsection