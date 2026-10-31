@extends('layouts.app')
@section('content')
<div class="container py-5 text-center">
  <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Magnifying%20Glass%20Tilted%20Left.png" width="100" class="mb-4">
  <h3>Halo, Anda belum memiliki lamaran aktif.</h3>
  <p class="text-muted">Silakan cari lowongan yang tersedia dan bergabung bersama kami.</p>
  <a href="{{ route('peserta.lowongan.index') }}" class="btn btn-primary btn-lg px-5 rounded-pill mt-3">Cari Lowongan</a>
</div>
@endsection