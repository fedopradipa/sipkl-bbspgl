@extends('layouts.guest')

@section('content')

{{-- === 1. HERO SECTION (TETAP SAMA) === --}}
<section class="page-header">
  <div class="container text-center position-relative z-1">
    <div class="header-ornament top"></div>
    <h1 class="display-4 fw-bold header-title">Lowongan PKL</h1>
    <p class="lead header-subtitle">Bergabunglah bersama Balai Besar Survei Pemetaan dan Geologi Kelautan</p>
    <div class="header-divider mx-auto mt-4"></div>
    <div class="header-ornament bottom"></div>
  </div>
  <div class="hero-pattern"></div>
</section>

<main class="py-5 main-content">
  <div class="container">

    {{-- === 2. FILTER & SEARCH SECTION (Guest Route) === --}}
    <div class="filter-section mb-5">
      <form action="{{ route('guest.home') }}" method="GET" class="row g-3 justify-content-center">
        <div class="col-lg-8">
          <div class="search-wrapper">
            <div class="search-icon text-primary">
              <i class="fas fa-search"></i>
            </div>
            <input type="text" name="keyword" class="form-control search-input"
              placeholder="Cari posisi, atau keahlian..."
              value="{{ request('keyword') }}">
            <button class="btn btn-search" type="submit">
              <span>Temukan</span>
              <i class="fas fa-arrow-right ms-2"></i>
            </button>
            @if(request('keyword'))
            <a href="{{ route('guest.home') }}" class="btn btn-reset" title="Reset"><i class="fas fa-times"></i></a>
            @endif
          </div>
        </div>
      </form>
    </div>

    {{-- === 3. PANGGIL PARTIAL GRID === --}}
    @include('partials.lowongan-grid', ['lowongan' => $lowongans])

  </div>
</main>
@endsection

@push('style')
<link rel="stylesheet" href="{{ asset('css/guest-landing.css') }}">
@endpush