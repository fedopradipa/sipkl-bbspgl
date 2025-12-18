@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

  {{-- Header Halaman --}}
  <div class="mb-4">
    <h4 class="fw-bold text-primary mb-1">Verifikasi Lamaran</h4>
    <p class="text-muted small mb-0">Kelola dan seleksi calon peserta magang yang masuk.</p>
  </div>

  {{-- Filter Bar --}}
  <div class="mb-4">
    @include('admin.lamaran.partials.filter_bar', ['currentStatus' => $currentStatus])
  </div>

  {{-- Tabel Data --}}
  <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-secondary">
          <tr>
            <th class="ps-4 py-3 border-bottom-0 text-uppercase small fw-bold">Pelamar</th>
            <th class="border-bottom-0 text-uppercase small fw-bold">Posisi & Jadwal Usulan</th>
            <th class="border-bottom-0 text-uppercase small fw-bold">Status</th>
            <th class="text-end pe-4 border-bottom-0 text-uppercase small fw-bold" style="min-width: 160px;">Aksi</th>
          </tr>
        </thead>
        <tbody class="border-top-0">
          @forelse($lamarans as $lamaran)
          <tr>
            {{-- KOLOM 1: PELAMAR --}}
            <td class="ps-4 py-3">
              <div class="d-flex align-items-center">
                {{-- Avatar Logic --}}
                @php
                $foto = $lamaran->pengguna->profile->foto_profil ?? null;
                $avatar = $foto
                ? asset('storage/foto_profil/'.$foto)
                : 'https://ui-avatars.com/api/?name='.urlencode($lamaran->pengguna->name).'&background=random&color=fff';
                @endphp
                <img src="{{ $avatar }}" class="rounded-circle me-3 border shadow-sm" width="45" height="45" style="object-fit: cover;">

                <div>
                  <div class="fw-bold text-dark mb-0">{{ $lamaran->pengguna->name }}</div>
                  <div class="small text-muted">{{ $lamaran->pengguna->email }}</div>
                </div>
              </div>
            </td>

            {{-- KOLOM 2: POSISI --}}
            <td>
              <div class="fw-bold text-dark mb-1">{{ $lamaran->lowongan->judul }}</div>
              <div class="d-flex align-items-center text-muted small">
                <div class="badge bg-light text-secondary border me-2">
                  <i class="far fa-calendar-alt me-1"></i> Usulan
                </div>
                <span>
                  {{ $lamaran->tgl_mulai_usulan->format('d M') }} -
                  {{ $lamaran->tgl_selesai_usulan->format('d M Y') }}
                </span>
              </div>
            </td>

            {{-- KOLOM 3: STATUS --}}
            <td>
              @include('admin.lamaran.components.status_badge', ['status' => $lamaran->status])
            </td>

            {{-- KOLOM 4: AKSI --}}
            <td class="text-end pe-4">
              <div class="d-flex justify-content-end gap-1">

                {{-- 1. Lihat Detail --}}
                <a href="{{ route('admin.lamaran.show', $lamaran->id) }}"
                  class="btn btn-info btn-sm text-white"
                  title="Lihat Detail">
                  <i class="fas fa-eye"></i>
                </a>

                {{-- 2. Terima Peserta (Hanya jika belum diterima) --}}
                @if($lamaran->status != 'diterima')
                <button type="button" class="btn btn-success btn-sm"
                  data-bs-toggle="modal"
                  data-bs-target="#editMagangModal"
                  data-mode="approve"
                  data-lamaran-id="{{ $lamaran->id }}"
                  data-start="{{ $lamaran->tgl_mulai_usulan->format('Y-m-d') }}"
                  data-end="{{ $lamaran->tgl_selesai_usulan->format('Y-m-d') }}"
                  data-days="{{ json_encode($lamaran->hari_kerja_usulan) }}"
                  data-pembimbing-id=""
                  title="Terima Peserta">
                  <i class="fas fa-check"></i>
                </button>
                @endif

                {{-- 3. Tolak (Hanya jika belum final) --}}
                @if(!in_array($lamaran->status, ['ditolak', 'diterima']))
                <button type="button" class="btn btn-warning btn-sm text-dark"
                  data-bs-toggle="modal"
                  data-bs-target="#rejectModal"
                  data-lamaran-id="{{ $lamaran->id }}"
                  title="Tolak Lamaran">
                  <i class="fas fa-times"></i>
                </button>
                @endif

                {{-- 4. Hapus --}}
                <form action="{{ route('admin.lamaran.destroy', $lamaran->id) }}" method="POST" class="d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm"
                    onclick="return confirm('Hapus permanen data lamaran ini?');"
                    title="Hapus Data">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>

              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="4" class="text-center py-5">
              <div class="mb-3 opacity-25">
                <i class="fas fa-inbox fa-4x text-muted"></i>
              </div>
              <h6 class="text-muted fw-bold">Belum ada data lamaran</h6>
              <p class="text-muted small mb-0">Coba ubah filter status atau kata kunci pencarian.</p>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    @if($lamarans->hasPages())
    <div class="card-footer bg-white border-top-0 py-3">
      {{ $lamarans->withQueryString()->links() }}
    </div>
    @endif
  </div>
</div>

{{-- Modals (Shared) --}}
@include('admin.shared.modals.edit-magang-modal')
@include('admin.lamaran.partials.reject_modal')

@endsection