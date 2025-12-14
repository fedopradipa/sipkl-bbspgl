@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

  {{-- Header & Filter Bar --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    @include('admin.lamaran.partials.filter_bar', ['currentStatus' => $currentStatus])
  </div>

  {{-- Tabel Data --}}
  <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-secondary">
          <tr>
            <th class="ps-4 py-3">Pelamar</th>
            <th>Posisi & Jadwal Usulan</th>
            <th>Status</th>
            <th class="text-end pe-4" style="min-width: 150px;">Aksi</th>
          </tr>
        </thead>
        <tbody class="border-top-0">
          @forelse($lamarans as $lamaran)
          <tr>
            {{-- Kolom Pelamar --}}
            <td class="ps-4">
              <div class="d-flex align-items-center">
                @if($lamaran->pengguna->profile && $lamaran->pengguna->profile->foto_profil)
                <img
                  src="{{ asset('storage/foto_profil/'.$lamaran->pengguna->profile->foto_profil) }}"
                  class="rounded-circle me-3 border"
                  width="40"
                  height="40"
                  style="object-fit: cover;">
                @else
                <img
                  src="https://ui-avatars.com/api/?name={{ urlencode($lamaran->pengguna->name) }}&background=random&color=fff"
                  class="rounded-circle me-3 border"
                  width="40"
                  height="40">
                @endif
                <div>
                  <div class="fw-bold text-dark">{{ $lamaran->pengguna->name }}</div>
                  <div class="small text-muted">{{ $lamaran->pengguna->email }}</div>
                </div>
              </div>
            </td>

            {{-- Kolom Posisi --}}
            <td>
              <div class="fw-bold text-primary mb-1">{{ $lamaran->lowongan->judul }}</div>
              <div class="small text-muted">
                <i class="far fa-calendar-alt me-1"></i>
                {{-- Menampilkan Jadwal USULAN --}}
                {{ $lamaran->tgl_mulai_usulan->format('d M') }} -
                {{ $lamaran->tgl_selesai_usulan->format('d M Y') }}
              </div>
            </td>

            {{-- Kolom Status --}}
            <td>
              @include('admin.lamaran.components.status_badge', ['status' => $lamaran->status])
            </td>

            {{-- Kolom Aksi --}}
            <td class="text-end pe-4">
              <div class="d-flex justify-content-end gap-1">

                {{-- 1. Lihat Detail --}}
                <a
                  class="btn btn-info btn-sm text-white"
                  href="{{ route('admin.lamaran.show', $lamaran->id) }}"
                  title="Lihat Detail">
                  <i class="fas fa-eye"></i>
                </a>

                {{-- 2. Terima Peserta --}}
                {{-- Hanya muncul jika status BELUM diterima --}}
                @if($lamaran->status != 'diterima')
                <button
                  type="button"
                  class="btn btn-success btn-sm"

                  {{-- Target ke Modal Shared --}}
                  data-bs-toggle="modal"
                  data-bs-target="#editMagangModal"

                  {{-- Mode Approve (Agar Modal jadi Hijau) --}}
                  data-mode="approve"

                  {{-- DATA DI SINI DIUBAH AGAR MENGAMBIL DARI USULAN --}}
                  data-lamaran-id="{{ $lamaran->id }}"

                  {{-- Ambil Tanggal dari USULAN --}}
                  data-start="{{ $lamaran->tgl_mulai_usulan->format('Y-m-d') }}"
                  data-end="{{ $lamaran->tgl_selesai_usulan->format('Y-m-d') }}"

                  {{-- Ambil Hari Kerja dari USULAN --}}
                  data-days="{{ json_encode($lamaran->hari_kerja_usulan) }}"

                  {{-- Pembimbing dikosongkan agar Admin memilih sendiri --}}
                  data-pembimbing-id=""

                  title="Terima Peserta">
                  <i class="fas fa-check"></i>
                </button>
                @endif

                {{-- 3. Tolak --}}
                @if($lamaran->status != 'ditolak' && $lamaran->status != 'diterima')
                <button
                  type="button"
                  class="btn btn-warning btn-sm"
                  data-bs-toggle="modal"
                  data-bs-target="#rejectModal"
                  data-lamaran-id="{{ $lamaran->id }}"
                  title="Tolak">
                  <i class="fas fa-times"></i>
                </button>
                @endif

                {{-- 4. Hapus --}}
                <form action="{{ route('admin.lamaran.destroy', $lamaran->id) }}" method="POST">
                  @csrf
                  @method('DELETE')
                  <button
                    type="submit"
                    class="btn btn-danger btn-sm"
                    onclick="return confirm('Hapus permanen data ini?');"
                    title="Hapus">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>

              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="4" class="text-center py-5">
              <div class="text-muted opacity-50 mb-2">
                <i class="fas fa-folder-open fa-3x"></i>
              </div>
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

{{-- Modals --}}
{{-- PENTING: Script JS untuk auto-fill sudah ada di dalam file modal ini --}}
@include('admin.shared.modals.edit-magang-modal')
@include('admin.lamaran.partials.reject_modal')

@endsection