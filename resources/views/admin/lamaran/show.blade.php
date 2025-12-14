@extends('layouts.app')

@section('content')
<div class="container-fluid">

  {{-- Data Preparation --}}
  @php
  // LOGIKA: Jika sudah diterima, ambil data resmi. Jika belum, ambil data usulan peserta.
  $dataStart = $lamaran->tgl_mulai_resmi
  ? $lamaran->tgl_mulai_resmi->format('Y-m-d')
  : $lamaran->tgl_mulai_usulan->format('Y-m-d');

  $dataEnd = $lamaran->tgl_selesai_resmi
  ? $lamaran->tgl_selesai_resmi->format('Y-m-d')
  : $lamaran->tgl_selesai_usulan->format('Y-m-d');

  $dataDays = $lamaran->hari_kerja_resmi
  ? json_encode($lamaran->hari_kerja_resmi)
  : json_encode($lamaran->hari_kerja_usulan);

  $pembimbingId = $lamaran->pembimbing_id ?? '';

  // ARRAY ATTRIBUTES (KEY HARUS SESUAI JS)
  $modalDataAttributes = [
  'lamaran-id' => $lamaran->id,
  'mode' => 'approve', // PENTING: Agar modal jadi Hijau
  'pembimbing-id' => $pembimbingId, // JS cari 'data-pembimbing-id'
  'start' => $dataStart, // JS cari 'data-start' (bukan resmi-start)
  'end' => $dataEnd, // JS cari 'data-end'
  'days' => $dataDays, // JS cari 'data-days'
  ];
  @endphp

  {{-- Main Grid --}}
  <div class="row g-4 mb-5 pb-5">

    {{-- Kolom Kiri: Info Lamaran --}}
    <div class="col-md-4">
      @include('admin.lamaran.components.info_card', ['lamaran' => $lamaran])
      @include('admin.lamaran.components.acceptance_card', ['lamaran' => $lamaran])
    </div>

    {{-- Kolom Kanan: Profil Peserta --}}
    <div class="col-md-8">
      @include('admin.lamaran.components.profile_card', ['lamaran' => $lamaran])
    </div>

  </div>

  {{-- Fixed Action Bar --}}
  <div class="fixed-bottom bg-white shadow-lg border-top py-3">
    <div class="container-fluid d-flex justify-content-between align-items-center">

      {{-- Tombol Kembali --}}
      <a href="{{ route('admin.lamaran.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Verifikasi
      </a>

      {{-- Tombol Aksi --}}
      @include('admin.lamaran.components.action_buttons', [
      'lamaran' => $lamaran,
      'modalDataAttributes' => $modalDataAttributes
      ])

    </div>
  </div>

  {{-- Modals --}}
  @include('admin.shared.modals.edit-magang-modal')
  @include('admin.lamaran.partials.reject_modal')

</div>
@endsection