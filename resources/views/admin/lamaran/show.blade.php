@extends('layouts.app')

@section('content')
<div class="container-fluid">

  {{-- Data Preparation (JANGAN DIUBAH) --}}
  @php
  $dataStart = $lamaran->tgl_mulai_resmi ? $lamaran->tgl_mulai_resmi->format('Y-m-d') : $lamaran->tgl_mulai_usulan->format('Y-m-d');
  $dataEnd = $lamaran->tgl_selesai_resmi ? $lamaran->tgl_selesai_resmi->format('Y-m-d') : $lamaran->tgl_selesai_usulan->format('Y-m-d');
  $dataDays = $lamaran->hari_kerja_resmi ? json_encode($lamaran->hari_kerja_resmi) : json_encode($lamaran->hari_kerja_usulan);
  $pembimbingId = $lamaran->pembimbing_id ?? '';

  $modalDataAttributes = [
  'lamaran-id' => $lamaran->id,
  'mode' => 'approve',
  'pembimbing-id' => $pembimbingId,
  'start' => $dataStart,
  'end' => $dataEnd,
  'days' => $dataDays,
  ];
  @endphp

  {{-- PAGE HEADER & ACTIONS --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-0 text-gray-800 fw-bold">Detail Lamaran Peserta</h1>
      <p class="text-muted mb-0">Tinjau data pelamar sebelum memberikan keputusan.</p>
    </div>

    {{-- Tombol Aksi di Header (Lebih UX Friendly) --}}
    <div class="d-flex gap-2">
      <a href="{{ route('admin.lamaran.index') }}" class="btn btn-secondary shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Kembali
      </a>

      {{-- Include Component Tombol --}}
      @include('admin.lamaran.components.action_buttons', [
      'lamaran' => $lamaran,
      'modalDataAttributes' => $modalDataAttributes
      ])
    </div>
  </div>

  {{-- ALERT JIKA DITOLAK --}}
  @if($lamaran->status == 'ditolak')
  <div class="alert alert-danger shadow-sm border-left-danger mb-4" role="alert">
    <h5 class="alert-heading fw-bold"><i class="fas fa-times-circle me-2"></i>Lamaran Ditolak</h5>
    <p class="mb-0">Alasan penolakan: <strong>{{ $lamaran->catatan_admin ?? '-' }}</strong></p>
  </div>
  @endif

  <div class="row">
    {{-- KOLOM KIRI (8): PROFIL UTAMA --}}
    <div class="col-lg-8 mb-4">
      @include('admin.lamaran.components.profile_card', ['lamaran' => $lamaran])
    </div>

    {{-- KOLOM KANAN (4): INFO & META DATA --}}
    <div class="col-lg-4 mb-4">
      @include('admin.lamaran.components.info_card', ['lamaran' => $lamaran])
    </div>
  </div>

  {{-- Modals --}}
  @include('admin.shared.modals.edit-magang-modal')
  @include('admin.lamaran.partials.reject_modal')

</div>
@endsection