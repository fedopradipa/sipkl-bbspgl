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
  @include('peserta.dashboard.components.state-active', [
  'isHariKerja' => $isHariKerja,
  'presensi' => $presensi,
  'lamaran' => $lamaran
  ])
  @break

  @case('selesai')
  @include('peserta.dashboard.components.state-selesai')
  @break
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

  .hover-scale {
    transition: transform 0.2s;
  }

  .hover-scale:hover {
    transform: scale(1.05);
  }

  .letter-spacing-2 {
    letter-spacing: 2px;
  }

  /* Pastikan z-index bekerja */
  .position-relative {
    position: relative;
  }

  .z-1 {
    z-index: 1;
  }
</style>
@endpush

{{-- Global Scripts --}}
@push('scripts')
<script src="{{ asset('js/geolocation.js') }}"></script>
@endpush

@endsection