@extends('layouts.app')

{{-- PENTING: Panggil CSS Guest disini agar tampilan SAMA PERSIS --}}
@push('styles')
<link rel="stylesheet" href="{{ asset('css/guest-landing.css') }}">
<style>
  /* Sedikit penyesuaian agar Hero Section tidak tertutup Navbar Dashboard */
  /* Jika Dashboard Anda punya padding, kita reset sedikit untuk halaman ini */
  .page-header-detail {
    margin-top: -1.5rem;
    /* Sesuaikan jika ada gap putih di atas */
    margin-left: -1.5rem;
    margin-right: -1.5rem;
    border-radius: 0 0 2rem 2rem;
    /* Lengkungan bawah saja */
  }

  /* Reset margin negatif container agar pas di dashboard */
  .mt-n5 {
    margin-top: -3rem !important;
  }
</style>
@endpush

@section('content')
{{-- Panggil Partial Tadi --}}
@include('partials.lowongan-detail', [
'lowongan' => $lowongan,
'backUrl' => route('peserta.lowongan.index')
])
@endsection