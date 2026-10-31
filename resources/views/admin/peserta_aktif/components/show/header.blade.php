{{-- Hitung Status Tanggal --}}
@php
$now = \Carbon\Carbon::now();
$endDate = \Carbon\Carbon::parse($item->tgl_selesai_resmi);
$isExpired = $now->greaterThan($endDate);
@endphp

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">

  {{-- BAGIAN KIRI: Tombol Kembali & posisi --}}
  <div>
    <a href="{{ route('admin.peserta_aktif.index') }}" class="text-decoration-none text-muted small mb-1 d-inline-block hover-arrow">
      <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Peserta
    </a>
    <h4 class="fw-bold text-dark m-0">
      Detail Peserta PKL
    </h4>
  </div>

  {{-- BAGIAN KANAN: Status & Action Bar --}}
  <div class="d-flex align-items-center gap-2 flex-wrap">

    {{-- 1. Status Badge (Lebih Modern dengan Border) --}}
    @if($isExpired)
    <div class="badge bg-danger-subtle text-danger border border-danger px-3 py-2 rounded-pill d-flex align-items-center">
      <i class="fas fa-exclamation-circle me-2"></i> Masa PKL Habis
    </div>
    @else
    <div class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill d-flex align-items-center">
      <i class="fas fa-check-circle me-2"></i> Status Aktif
    </div>
    @endif

    {{-- Separator Vertikal --}}
    <div class="vr mx-1 text-muted d-none d-md-block" style="height: 30px;"></div>

    {{-- 2. Tombol EDIT (Kuning - Koreksi) --}}
    {{-- Kita pasang Data Attributes agar modal Edit otomatis terisi --}}
    <button type="button" class="btn btn-warning btn-sm fw-bold text-dark"
      data-bs-toggle="modal"
      data-bs-target="#editMagangModal"
      data-lamaran-id="{{ $item->id }}"
      data-pembimbing-id="{{ $item->pembimbing_id }}"
      data-start="{{ $item->tgl_mulai_resmi ? $item->tgl_mulai_resmi->format('Y-m-d') : '' }}"
      data-end="{{ $item->tgl_selesai_resmi ? $item->tgl_selesai_resmi->format('Y-m-d') : '' }}"
      data-days="{{ json_encode($item->hari_kerja_resmi ?? []) }}"
      title="Edit Data / Perpanjang">
      <i class="fas fa-pen me-1"></i> Edit
    </button>

    {{-- 3. Tombol DROP OUT (Merah Outline - Destruktif) --}}
    <button type="button" class="btn btn-outline-danger btn-sm fw-bold"
      data-bs-toggle="modal"
      data-bs-target="#dropoutModal{{ $item->id }}"
      title="Hentikan Peserta">
      <i class="fas fa-ban me-1"></i> Hentikan
    </button>

    {{-- 4. Tombol LULUSKAN (Hijau Solid - Primary Action) --}}
    {{-- Jika expired, beri animasi blink agar Admin sadar harus segera meluluskan --}}
    <button type="button" class="btn btn-success btn-sm fw-bold px-3 {{ $isExpired ? 'blink-animation' : '' }}"
      data-bs-toggle="modal"
      data-bs-target="#lulusModal{{ $item->id }}"
      title="Luluskan Peserta">
      <i class="fas fa-graduation-cap me-1"></i> Luluskan
    </button>

  </div>
</div>

{{-- INCLUDE MODAL PARTIALS --}}
{{-- 1. Modal Edit (Shared) --}}
@include('admin.shared.modals.edit-magang-modal')
{{-- PENTING: Sertakan file modal yang sudah kita pisahkan tadi di sini --}}
@include('admin.peserta_aktif.partials.modal_lulus', ['item' => $item, 'isOverdue' => $isExpired])
@include('admin.peserta_aktif.partials.modal_dropout', ['item' => $item])