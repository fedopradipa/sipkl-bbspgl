@extends('layouts.app')

@section('content')
<div class="container py-4">

  {{-- Header Sapaan --}}
  @include('peserta.dashboard.components.header', ['user' => $user])

  {{-- State-Based UI --}}
  @switch($state)
  @case('pending')
  @include('peserta.dashboard.components.state-pending', ['lamaran' => $lamaran])
  @break

  @case('ditolak')
  @include('peserta.dashboard.components.state-ditolak', ['lamaran' => $lamaran])
  @break

  @case('pra_mulai')
  @include('peserta.dashboard.components.state-pra-mulai', ['lamaran' => $lamaran])
  @break

  @case('active')
  {{-- PERUBAHAN DISINI: Hapus 'presensi' dan 'isHariKerja' --}}
  {{-- Cukup kirim 'lamaran' saja --}}
  @include('peserta.dashboard.components.state-active', [
  'lamaran' => $lamaran
  ])
  @break

  @case('finalisasi')
  @include('peserta.dashboard.components.state-finalisasi', ['lamaran' => $lamaran])
  @break

  @case('selesai')
  @include('peserta.dashboard.components.state-selesai', ['lamaran' => $lamaran])
  @break

  @case('berhenti')
  @include('peserta.dashboard.components.state-berhenti', ['lamaran' => $lamaran])
  @break

  @default
  {{-- State Kosong / Belum Melamar --}}
  @include('peserta.dashboard.empty')
  @endswitch

</div>

{{-- Global Styles --}}
@push('styles')
<style>
  .bg-gradient-primary {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%) !important;
  }

  .hover-up {
    transition: transform 0.2s, box-shadow 0.2s;
  }

  .hover-up:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
  }

  /* Style lainnya tetap sama */
</style>
@endpush

{{-- Global Scripts --}}
@endsection