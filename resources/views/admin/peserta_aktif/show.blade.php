@extends('layouts.app')

@section('content')
<div class="container-fluid">

  {{-- Header with Breadcrumb & Actions --}}
  @include('admin.peserta_aktif.components.show.header', ['item' => $item])

  <div class="row g-4">

    {{-- Left Column: Profile Card --}}
    <div class="col-lg-4">
      @include('admin.peserta_aktif.components.show.profile-card', ['item' => $item])
    </div>

    {{-- Right Column: Details & Documents --}}
    <div class="col-lg-8">

      {{-- Main Info Card with Tabs --}}
      @include('admin.peserta_aktif.components.show.info-tabs', ['item' => $item])

    </div>
  </div>

</div>
@endsection

@push('styles')
<style>
  .badge-status {
    padding: 0.5rem 1rem;
    font-weight: 600;
    font-size: 0.875rem;
  }

  .info-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.5px;
    color: #6c757d;
    margin-bottom: 0.25rem;
  }

  .info-value {
    font-weight: 600;
    color: #212529;
    font-size: 1rem;
  }

  .nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 600;
    padding: 0.75rem 1.25rem;
  }

  .nav-tabs .nav-link.active {
    color: #0d6efd;
    border-bottom: 3px solid #0d6efd;
    background: transparent;
  }

  .timeline-item {
    position: relative;
    padding-left: 2rem;
    padding-bottom: 1.5rem;
  }

  .timeline-item:before {
    content: '';
    position: absolute;
    left: 0.5rem;
    top: 0.5rem;
    bottom: -1.5rem;
    width: 2px;
    background: #e9ecef;
  }

  .timeline-item:last-child:before {
    display: none;
  }

  .timeline-icon {
    position: absolute;
    left: 0;
    top: 0;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    z-index: 1;
  }
</style>
@endpush