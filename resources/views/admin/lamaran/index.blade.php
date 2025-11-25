@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary">Daftar Lamaran (Status: {{ ucfirst($currentStatus) }})</h6>
  </div>
  <div class="card-body">

    {{-- Tombol Filter Status --}}
    <div class="mb-3 d-flex gap-2">
      @foreach(['pending', 'diterima', 'ditolak', 'all'] as $status)
      <a href="{{ route('lamaran.index.admin', ['status' => $status, 'keyword' => request('keyword')]) }}"
        class="btn btn-sm @if($currentStatus == $status) btn-primary @else btn-outline-secondary @endif text-capitalize">
        {{ $status }} ({{ \App\Models\Lamaran::where('status', $status)->count() }})
      </a>
      @endforeach
    </div>

    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Pelamar</th>
            <th>Posisi Dilamar</th>
            <th>Durasi Usulan</th>
            <th>Hari Kerja Usulan</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($lamarans as $lamaran)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
              <div class="fw-bold">{{ $lamaran->pengguna->name }}</div>
              <small class="text-muted">{{ $lamaran->pengguna->email }}</small>
            </td>
            <td>
              <div class="fw-bold text-primary">{{ $lamaran->lowongan->judul }}</div>
              <small>{{ $lamaran->lowongan->divisi }}</small>
            </td>
            <td>{{ $lamaran->tgl_mulai_usulan->format('d M Y') }} s/d {{ $lamaran->tgl_selesai_usulan->format('d M Y') }}</td>
            <td class="text-nowrap">
              @php
              // Hapus json_decode() karena Model sudah melakukan casting ke 'array'
              $hariKerja = $lamaran->hari_kerja_usulan;
              @endphp

              @if(is_array($hariKerja))
              @foreach($hariKerja as $day)
              <span class="badge bg-secondary me-1">{{ $day }}</span>
              @endforeach
              @else
              <span class="text-danger small">Error Data</span>
              @endif
            </td>
            <td>
              <span class="badge bg-{{ $lamaran->status === 'pending' ? 'warning text-dark' : ($lamaran->status === 'diterima' ? 'success' : 'danger') }} text-capitalize">
                {{ $lamaran->status }}
              </span>
            </td>
            <td class="text-nowrap">
              {{-- TOMBOL SHOW / DETAIL (Selalu Muncul) --}}
              <a href="{{ route('lamaran.show.admin', $lamaran->id) }}" class="btn btn-info btn-sm me-1 text-white">
                <i class="fas fa-eye"></i> Lihat
              </a>

              {{-- TOMBOL TERIMA/TOLAK (Hanya muncul jika Pending) --}}
              @if($lamaran->status == 'pending')
              <button class="btn btn-success btn-sm me-1" data-bs-toggle="modal" data-bs-target="#approveModal"
                data-lamaran-id="{{ $lamaran->id }}"
                data-lowongan-id="{{ $lamaran->lowongan->id }}"
                data-start-usulan="{{ $lamaran->tgl_mulai_usulan->format('Y-m-d') }}"
                data-end-usulan="{{ $lamaran->tgl_selesai_usulan->format('Y-m-d') }}"
                data-days-usulan="{{ json_encode($lamaran->hari_kerja_usulan) }}"
                title="Set Pembimbing & Tanggal Resmi">
                Terima
              </button>
              <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal" data-lamaran-id="{{ $lamaran->id }}">
                Tolak
              </button>
              @else
              {{-- Opsional: Jika status sudah final, tampilkan label info saja --}}
              {{-- <span class="text-muted small fst-italic">Selesai</span> --}}
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center">Tidak ada lamaran dengan status ini.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    {{ $lamarans->withQueryString()->links() }}
  </div>
</div>

@include('admin.lamaran.partials.approve_modal')
@include('admin.lamaran.partials.reject_modal')

@endsection