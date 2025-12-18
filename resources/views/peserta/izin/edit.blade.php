@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-8">

      {{-- Error handling --}}
      @if ($errors->any())
      <div class="alert alert-danger mb-3">
        <ul class="mb-0">
          @foreach($errors->all() as $e) <li>{{$e}}</li> @endforeach
        </ul>
      </div>
      @endif

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-warning bg-opacity-10 py-3">
          <h5 class="mb-0 fw-bold text-dark">Edit Pengajuan Izin</h5>
        </div>
        <div class="card-body p-4">
          {{-- Panggil Form Partial, kirim variabel $izin --}}
          @include('peserta.izin.partials.form', ['izin' => $izin])
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
{{-- Panggil Script Partial, kirim variabel $izin dan $lamaran --}}
@include('peserta.izin.partials.scripts', ['lamaran' => $lamaran, 'izin' => $izin])
@endpush