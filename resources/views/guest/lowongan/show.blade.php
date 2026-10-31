@extends('layouts.guest')

@section('content')
{{-- Panggil Partial, kirim route untuk tombol 'Kembali' --}}
@include('partials.lowongan-detail', [
'lowongan' => $lowongan,
'backRoute' => route('guest.home')
])
@endsection

@push('style')
<link rel="stylesheet" href="{{ asset('css/guest-landing.css') }}">
@endpush