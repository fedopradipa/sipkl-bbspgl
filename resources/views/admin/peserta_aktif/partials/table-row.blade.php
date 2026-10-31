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
          <i class="fas fa-exclamation-circle me-1"></i> Masa PKL Habis
        </span>
        @endif
      </div>
    </div>
  </td>

  {{-- KOLOM 2: POSISI --}}
  <td>
    <div class="fw-bold text-dark">{{ $item->lowongan->nama_posisi ?? $item->lowongan->posisi }}</div>

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
    </div>

    {{-- INCLUDE MODAL PARTIALS --}}
    {{-- Kita passing variabel yang dibutuhkan ke dalam partial --}}
    @include('admin.peserta_aktif.partials.modal_lulus', ['item' => $item, 'isOverdue' => $isOverdue])
    @include('admin.peserta_aktif.partials.modal_dropout', ['item' => $item])

  </td>
</tr>