@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

  {{-- HEADER & FILTER BAR --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">

    {{-- Filter Form --}}
    <form action="{{ route('lamaran.index.admin') }}" method="GET" class="d-flex gap-2 w-100 w-md-auto">
      {{-- Filter Status --}}
      <div class="btn-group shadow-sm">
        @foreach(['pending', 'diterima', 'ditolak', 'all'] as $st)
        <a href="{{ route('lamaran.index.admin', array_merge(request()->query(), ['status' => $st])) }}"
          class="btn btn-sm {{ $currentStatus == $st ? 'btn-primary' : 'btn-white border' }} text-capitalize">
          {{ $st }}
        </a>
        @endforeach
      </div>

      {{-- Filter Lowongan --}}
      <select name="lowongan_id" class="form-select form-select-sm shadow-sm" style="max-width: 200px;" onchange="this.form.submit()">
        <option value="">Semua Posisi</option>
        @foreach(\App\Models\Lowongan::all() as $l)
        <option value="{{ $l->id }}" {{ request('lowongan_id') == $l->id ? 'selected' : '' }}>
          {{ $l->judul }}
        </option>
        @endforeach
      </select>

      {{-- Search --}}
      <div class="input-group input-group-sm shadow-sm" style="max-width: 250px;">
        <input type="text" name="keyword" class="form-control" placeholder="Cari nama..." value="{{ request('keyword') }}">
        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
      </div>
    </form>
  </div>

  {{-- TABEL DATA --}}
  <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-secondary">
          <tr>
            <th class="ps-4 py-3">Pelamar</th>
            <th>Posisi & Jadwal</th>
            <th>Status</th>
            <th class="text-end pe-4" style="min-width: 150px;">Aksi</th>
          </tr>
        </thead>
        <tbody class="border-top-0">
          @forelse($lamarans as $lamaran)
          <tr>
            {{-- KOLOM 1: PELAMAR --}}
            <td class="ps-4">
              <div class="d-flex align-items-center">
                @if($lamaran->pengguna->foto_profil)
                <img src="{{ asset('storage/foto_profil/'.$lamaran->pengguna->foto_profil) }}" class="rounded-circle me-3 border" width="40" height="40" style="object-fit: cover;">
                @else
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3 border" style="width: 40px; height: 40px;">
                  <span class="fw-bold text-secondary">{{ substr($lamaran->pengguna->name, 0, 1) }}</span>
                </div>
                @endif
                <div>
                  <div class="fw-bold text-dark">{{ $lamaran->pengguna->name }}</div>
                  <div class="small text-muted">{{ $lamaran->pengguna->email }}</div>
                </div>
              </div>
            </td>

            {{-- KOLOM 2: POSISI --}}
            <td>
              <div class="fw-bold text-primary mb-1">{{ $lamaran->lowongan->judul }}</div>
              <div class="small text-muted">
                <i class="far fa-calendar-alt me-1"></i>
                {{ $lamaran->tgl_mulai_usulan->format('d M') }} - {{ $lamaran->tgl_selesai_usulan->format('d M Y') }}
              </div>
            </td>

            {{-- KOLOM 3: STATUS --}}
            <td>
              @if($lamaran->status == 'pending')
              <span class="badge bg-warning text-dark bg-opacity-25 border border-warning px-3 py-2 rounded-pill">
                <i class="fas fa-hourglass-half me-1"></i> Menunggu
              </span>
              @elseif($lamaran->status == 'diterima')
              <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill">
                <i class="fas fa-check-circle me-1"></i> Diterima
              </span>
              @else
              <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill">
                <i class="fas fa-times-circle me-1"></i> Ditolak
              </span>
              @endif
            </td>

            {{-- KOLOM 4: AKSI (Fixed) --}}
            <td class="text-end pe-4">

              {{-- 1. TOMBOL DETAIL (Direct Link ke Show) --}}
              <a href="{{ route('lamaran.show.admin', $lamaran->id) }}"
                class="btn btn-sm btn-outline-info me-1"
                title="Lihat Detail Lengkap">
                <i class="fas fa-eye"></i> Detail
              </a>

              {{-- 2. DROPDOWN AKSI (Terima/Tolak/Hapus) --}}
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                  Proses
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">

                  {{-- Menu TERIMA / EDIT --}}
                  <li>
                    <button class="dropdown-item text-success fw-bold"
                      data-bs-toggle="modal"
                      data-bs-target="#approveModal"
                      {{-- Data Attributes --}}
                      data-lamaran-id="{{ $lamaran->id }}"
                      data-current-status="{{ $lamaran->status }}"
                      data-usulan-start="{{ $lamaran->tgl_mulai_usulan->format('Y-m-d') }}"
                      data-usulan-end="{{ $lamaran->tgl_selesai_usulan->format('Y-m-d') }}"
                      data-resmi-pembimbing="{{ $lamaran->pembimbing_id }}"
                      data-resmi-start="{{ $lamaran->tgl_mulai_resmi ? $lamaran->tgl_mulai_resmi->format('Y-m-d') : $lamaran->tgl_mulai_usulan->format('Y-m-d') }}"
                      data-resmi-end="{{ $lamaran->tgl_selesai_resmi ? $lamaran->tgl_selesai_resmi->format('Y-m-d') : $lamaran->tgl_selesai_usulan->format('Y-m-d') }}"
                      data-resmi-days="{{ $lamaran->hari_kerja_resmi ? json_encode($lamaran->hari_kerja_resmi) : json_encode($lamaran->hari_kerja_usulan) }}">
                      <i class="fas fa-check me-2"></i> {{ $lamaran->status == 'diterima' ? 'Edit Data' : 'Terima' }}
                    </button>
                  </li>

                  {{-- Menu TOLAK --}}
                  @if($lamaran->status != 'ditolak')
                  <li>
                    <button class="dropdown-item text-warning fw-bold"
                      data-bs-toggle="modal" data-bs-target="#rejectModal" data-lamaran-id="{{ $lamaran->id }}">
                      <i class="fas fa-times me-2"></i> Tolak
                    </button>
                  </li>
                  @endif

                  <li>
                    <hr class="dropdown-divider">
                  </li>

                  {{-- Menu HAPUS --}}
                  <li>
                    <form action="{{ route('lamaran.destroy', $lamaran->id) }}" method="POST" onsubmit="return confirm('Hapus permanen data ini?');">
                      @csrf @method('DELETE')
                      <button type="submit" class="dropdown-item text-danger">
                        <i class="fas fa-trash me-2"></i> Hapus Permanen
                      </button>
                    </form>
                  </li>
                </ul>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="4" class="text-center py-5">
              <div class="text-muted opacity-50 mb-2"><i class="fas fa-folder-open fa-3x"></i></div>
              <p class="text-muted">Tidak ada data lamaran sesuai filter.</p>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="card-footer bg-white border-0 py-3">
      {{ $lamarans->withQueryString()->links() }}
    </div>
  </div>
</div>

{{-- Include Modal yang sudah ada --}}
@include('admin.lamaran.partials.approve_modal')
@include('admin.lamaran.partials.reject_modal')

@endsection