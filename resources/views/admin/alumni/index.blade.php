@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
  <h3 class="mt-4 mb-4">Data Alumni & Arsip</h3>

  <div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="bg-light">
          <tr>
            <th class="ps-4">Nama Peserta</th>
            <th>Periode Magang</th>
            <th>Status Akhir</th>
            <th>Dokumen / Catatan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($alumni as $item)
          <tr>
            <td class="ps-4">
              <div class="fw-bold">{{ $item->pengguna->name }}</div>
              <div class="small text-muted">{{ $item->lowongan->judul }}</div>
            </td>
            <td>
              {{ $item->tgl_mulai_resmi->format('d M Y') }} -
              {{ $item->tgl_selesai_resmi->format('d M Y') }}
            </td>
            <td>
              @if($item->status == 'selesai')
              <span class="badge bg-success">Lulus</span>
              @else
              <span class="badge bg-secondary">Drop Out</span>
              @endif
            </td>
            <td>
              @if($item->status == 'selesai')
              <button class="btn btn-sm btn-outline-primary"><i class="fas fa-certificate"></i> Sertifikat</button>
              @else
              <button class="btn btn-sm btn-light text-danger" onclick="alert('Alasan: {{ $item->catatan_admin }}')">
                <i class="fas fa-info-circle"></i> Alasan
              </button>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="4" class="text-center p-4">Belum ada data alumni.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer bg-white border-0">
      {{ $alumni->links() }}
    </div>
  </div>
</div>
@endsection