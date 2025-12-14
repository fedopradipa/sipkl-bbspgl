@extends('layouts.app')

@section('content')
<div class="container-fluid">

  {{-- 1. PANGGIL PARTIAL HEADER --}}
  @include('admin.peserta_aktif.partials.header')

  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-secondary">
            <tr>
              <th class="ps-4">Peserta</th>
              <th>Posisi & Divisi</th>
              <th width="25%">Durasi & Progress</th>
              <th>Pembimbing</th>
              <th class="text-end pe-4">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($peserta as $item)
            {{-- 2. PANGGIL PARTIAL ROW --}}
            {{-- Baris ini berisi tombol Edit dengan data-attributes yang lengkap --}}
            @include('admin.peserta_aktif.partials.table-row', ['item' => $item])
            @empty
            <tr>
              <td colspan="5" class="text-center py-5 text-muted">
                <p class="mb-0">Tidak ada peserta aktif ditemukan.</p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Pagination --}}
    <div class="card-footer bg-white border-0 py-3">
      {{ $peserta->withQueryString()->links() }}
    </div>
  </div>
</div>

{{-- ==================================================== --}}
{{-- BAGIAN BARU: INCLUDE SHARED MODAL & SCRIPT --}}
{{-- ==================================================== --}}

{{-- 1. Panggil Modal Shared (Lokasi: resources/views/admin/shared/modals/edit-magang-modal.blade.php) --}}
@include('admin.shared.modals.edit-magang-modal')

@endsection