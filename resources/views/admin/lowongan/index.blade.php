@extends('layouts.admin')

@section('content')
{{-- TABEL --}}
<div class="card mb-4">
  <div class="card-body">
    <h5 class="card-title">Data Lowongan PKL</h5>
    {{-- Controls: Add, Filter, Search --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
      <div class="d-flex flex-wrap align-items-center">
        <a class="btn btn-primary btn-sm mb-2 me-2" href="{{ route('lowongan.create') }}">
          <i class="fas fa-plus me-1"></i> Tambah Lowongan
        </a>
      </div>
      <div class="col-12 col-md-auto">
        {{-- Form untuk pencarian --}}
        <form action="{{ route('lowongan.index') }}" method="GET" class="d-flex align-items-center">

          <input type="text" name="keyword" class="form-control form-control-sm me-2" placeholder="Cari lowongan..." value="{{ request('keyword') }}">

          {{-- Tombol Cari dengan Ikon --}}
          <button type="submit" class="btn btn-secondary btn-sm" title="Cari">
            <i class="fas fa-search"></i>
          </button>

          {{-- TOMBOL RESET DENGAN IKON --}}
          {{-- Tampilkan tombol ini hanya jika ada parameter 'keyword' di URL --}}
          @if(request('keyword'))
          <a href="{{ route('lowongan.index') }}" class="btn btn-danger btn-sm ms-2" title="Reset">
            <i class="fas fa-sync-alt"></i>
          </a>
          @endif
        </form>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Judul</th>
            <th scope="col">Divisi</th>
            <th scope="col">Kuota</th>
            <th scope="col">Periode Pendaftaran</th>
            <th scope="col">Jam Kerja</th>
            <th scope="col">Status</th>
            <th scope="col">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($lowongans as $item)
          <tr>
            <th scope="row">{{ ($lowongans->currentPage() - 1) * $lowongans->perPage() + $loop->iteration }}</th>
            <td>{{ $item->judul }}</td>
            <td>{{ $item->divisi }}</td>
            <td>{{ $item->kuota }}</td>
            <td>{{ \Carbon\Carbon::parse($item->window_start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($item->window_end_date)->format('d M Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($item->jam_masuk_standar)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->jam_pulang_standar)->format('H:i') }}</td>
            <td>
              {{-- Logika Visualisasi Status --}}
              @if(!$item->is_open)
              {{-- 1. Admin sengaja mematikan --}}
              <span class="badge bg-danger">Ditutup (Manual)</span>

              @elseif($item->is_open && \Carbon\Carbon::now()->gt($item->window_end_date))
              {{-- 2. Admin lupa mematikan, tapi tanggal sudah lewat --}}
              <span class="badge bg-warning text-dark">
                <i class="fas fa-clock me-1"></i> Kedaluwarsa
              </span>
              <br>

              @elseif($item->is_open && \Carbon\Carbon::now()->lt($item->window_start_date))
              {{-- 3. Belum waktunya buka --}}
              <span class="badge bg-info text-dark">Belum Dibuka</span>

              @else
              {{-- 4. Aktif normal --}}
              <span class="badge bg-success">Aktif & Tayang</span>
              @endif
            </td>

            <td class="align-top">
              <div class="d-flex gap-1">
                <a class="btn btn-info btn-sm" href="{{ route('lowongan.show', $item->id) }}" title="Lihat Detail"><i class="fas fa-eye"></i></a>
                <a class="btn btn-warning btn-sm" href="{{ route('lowongan.edit', $item->id) }}" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                <form action="{{ route('lowongan.destroy', $item->id) }}" method="POST">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus lowongan ini?')" title="Hapus">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center">
              @if(request('keyword'))
              Lowongan dengan kata kunci "{{ request('keyword') }}" tidak ditemukan.
              @else
              Tidak ada data lowongan.
              @endif
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- BAGIAN PAGINATION --}}
    <div class="d-flex justify-content-end mt-3">
      {{ $lowongans->links() }}
    </div>
  </div>
</div>
@endsection