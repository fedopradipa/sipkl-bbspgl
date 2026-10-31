@extends('layouts.app')

@section('content')
<!-- TABEL -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Data Pengguna</h5>
        <!-- Controls: Add, Filter, Search -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div class="d-flex flex-wrap align-items-center">
                {{-- Diperbarui ke user.create --}}
                <a class="btn btn-primary btn-sm mb-2 me-2" href="{{ route('user.create') }}">
                    <i class="fas fa-plus me-1"></i> Tambah Pengguna
                </a>
            </div>
            <div class="col-12 col-md-auto">
                {{-- Form untuk pencarian --}}
                {{-- Diperbarui ke user.index --}}
                <form action="{{ route('user.index') }}" method="GET" class="d-flex align-items-center">

                    <input type="text" name="keyword" class="form-control form-control-sm me-2"
                        placeholder="Cari nama/email..." value="{{ request('keyword') }}">

                    {{-- Tombol Cari dengan Ikon --}}
                    <button type="submit" class="btn btn-secondary btn-sm" title="Cari">
                        <i class="fas fa-search"></i>
                    </button>

                    {{-- TOMBOL RESET DENGAN IKON --}}
                    {{-- Diperbarui ke user.index --}}
                    @if(request('keyword'))
                    <a href="{{ route('user.index') }}" class="btn btn-danger btn-sm ms-2" title="Reset">
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
                        <th scope="col">Nama Pengguna</th>
                        <th scope="col">Email</th> {{-- <-- DARI TEMPLATE --}}
                        <th scope="col">Peran (Role)</th> {{-- <-- BARU --}}
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Diperbarui ke $users --}}
                    @forelse ($users as $item)
                    <tr>
                        {{-- Diperbarui ke $users --}}
                        <th scope="row">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</th>

                        {{-- Diperbarui ke item->name (Sesuai Model User) --}}
                        <td>{{ $item->name }}</td>

                        {{-- Diperbarui ke item->email (Sesuai Model User) --}}
                        <td>{{ $item->email }}</td>

                        {{-- Menampilkan relasi role (Sesuai Model User) --}}
                        <td>
                            <span class="badge {{ $item->isSuperadmin() ? 'bg-danger' : 'bg-primary' }}">
                                {{ $item->role?->nama_role ?? 'Tanpa Peran' }}
                            </span>
                        </td>

                        <td class="align-top">
                            <div class="d-flex gap-1">
                                {{-- Diperbarui ke user.* dan $item->id --}}
                                <!-- <a class="btn btn-info btn-sm" href="{{ route('user.show', $item->id) }}"><i class="fas fa-eye"></i></a> -->
                                <a class="btn btn-warning btn-sm" href="{{ route('user.edit', $item->id) }}"><i class="fas fa-pencil-alt"></i></a>

                                {{--
                                  Tambahkan @if agar Superadmin tidak bisa 
                                  menghapus akunnya sendiri (Sesuai Controller) 
                                --}}
                                @if(auth()->id() != $item->id)
                                <form action="{{ route('user.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        {{-- colspan disesuaikan menjadi 5 --}}
                        <td colspan="5" class="text-center">
                            @if(request('keyword'))
                            Pengguna dengan kata kunci "{{ request('keyword') }}" tidak ditemukan.
                            @else
                            Tidak ada data pengguna.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- BAGIAN PAGINATION --}}
        {{-- Diperbarui ke $users --}}
        <div class="d-flex justify-content-end mt-3">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection