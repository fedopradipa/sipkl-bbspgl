@php
// Cek Overdue
$isOverdue = \Carbon\Carbon::now()->gt($item->tgl_selesai_resmi);
$rowClass = $isOverdue ? 'bg-light-danger border-start border-danger border-3' : '';

// Setup Avatar
$filename = $item->pengguna->profile->foto_profil ?? null;
$fotoUrl = $filename ? asset('storage/foto_profil/' . $filename) : 'https://ui-avatars.com/api/?name='.urlencode($item->pengguna->name).'&background=random&color=fff';
@endphp

<tr class="align-middle {{ $rowClass }}">

  {{-- KOLOM 1: PROFIL --}}
  <td class="ps-3 py-3">
    <div class="d-flex align-items-center">
      <img src="{{ $fotoUrl }}" class="rounded-circle shadow-sm me-3 border" width="45" height="45" style="object-fit: cover;">
      <div>
        <div class="fw-bold text-dark mb-0">{{ $item->pengguna->name }}</div>
        <div class="small text-muted mb-1">{{ $item->pengguna->email }}</div>

        @if($isOverdue)
        <span class="badge bg-danger blink-animation small">
          <i class="fas fa-exclamation-circle me-1"></i> Masa Magang Habis
        </span>
        @endif
      </div>
    </div>
  </td>

  {{-- KOLOM 2: POSISI & DIVISI --}}
  <td>
    <div class="fw-bold text-dark">{{ $item->lowongan->nama_posisi ?? $item->lowongan->judul }}</div>
    <div class="badge bg-light text-secondary border mt-1">
      <i class="fas fa-building me-1"></i> {{ $item->lowongan->divisi }}
    </div>
  </td>

  {{-- KOLOM 3: PROGRESS BAR --}}
  <td style="min-width: 180px;">
    @include('admin.peserta_aktif.components.progress-bar', [
    'start' => $item->tgl_mulai_resmi,
    'end' => $item->tgl_selesai_resmi
    ])
  </td>

  {{-- KOLOM 4: PEMBIMBING --}}
  <td>
    @if($item->pembimbing)
    <div class="d-flex align-items-center">
      <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
        <i class="fas fa-user-tie text-primary small"></i>
      </div>
      <span class="small fw-bold text-secondary">{{ $item->pembimbing->nama }}</span>
    </div>
    @else
    <span class="badge bg-secondary text-white">Belum ditentukan</span>
    @endif
  </td>

  {{-- KOLOM 5: AKSI --}}
  <td class="text-end pe-3">
    <div class="d-flex gap-1 justify-content-end">

      {{-- 1. DETAIL --}}
      <a href="{{ route('admin.peserta_aktif.show', $item->id) }}" class="btn btn-info btn-sm text-white" title="Detail Peserta">
        <i class="fas fa-eye"></i>
      </a>

      {{-- 2. EDIT --}}
      <button type="button" class="btn btn-warning btn-sm text-dark fw-bold"
        data-bs-toggle="modal" data-bs-target="#editMagangModal"
        data-lamaran-id="{{ $item->id }}"
        data-pembimbing-id="{{ $item->pembimbing_id }}"
        data-start="{{ $item->tgl_mulai_resmi ? $item->tgl_mulai_resmi->format('Y-m-d') : '' }}"
        data-end="{{ $item->tgl_selesai_resmi ? $item->tgl_selesai_resmi->format('Y-m-d') : '' }}"
        data-days="{{ json_encode($item->hari_kerja_resmi ?? []) }}"
        title="Edit Data / Perpanjang">
        <i class="fas fa-pen"></i>
      </button>

      {{-- 3. LULUSKAN --}}
      <button type="button" class="btn btn-success btn-sm {{ $isOverdue ? 'blink-animation' : '' }}"
        data-bs-toggle="modal" data-bs-target="#lulusModal{{ $item->id }}"
        title="Luluskan Peserta">
        <i class="fas fa-graduation-cap"></i>
      </button>

      {{-- 4. DROP OUT --}}
      <button type="button" class="btn btn-danger btn-sm"
        data-bs-toggle="modal" data-bs-target="#dropoutModal{{ $item->id }}"
        title="Hentikan / Drop Out">
        <i class="fas fa-ban"></i>
      </button>
    </div>

    {{-- INCLUDE MODAL PARTIALS --}}
    {{-- Kita passing variabel yang dibutuhkan ke dalam partial --}}
    @include('admin.peserta_aktif.partials.modal_lulus', ['item' => $item, 'isOverdue' => $isOverdue])
    @include('admin.peserta_aktif.partials.modal_dropout', ['item' => $item])

  </td>
</tr>