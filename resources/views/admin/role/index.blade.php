@extends('layouts.admin')

@section('content')
<!-- TABEL -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Data Peran (Role)</h5>
        <!-- Controls: Add, Filter, Search -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div class="d-flex flex-wrap align-items-center">
                {{-- Diperbarui ke role.create --}}
                <a class="btn btn-primary btn-sm mb-2 me-2" href="{{ route('role.create') }}">
                    <i class="fas fa-plus me-1"></i> Tambah Peran
                </a>
            </div>
            <div class="col-12 col-md-auto">
                {{-- Form untuk pencarian --}}
                {{-- Diperbarui ke role.index --}}
                <form action="{{ route('role.index') }}" method="GET" class="d-flex align-items-center">
                    
                    <input type="text" name="keyword" class="form-control form-control-sm me-2" 
                           placeholder="Cari peran..." value="{{ request('keyword') }}">
                    
                    {{-- Tombol Cari dengan Ikon --}}
                    <button type="submit" class="btn btn-secondary btn-sm" title="Cari">
                        <i class="fas fa-search"></i>
                    </button>
                    
                    {{-- TOMBOL RESET DENGAN IKON --}}
                    {{-- Diperbarui ke role.index --}}
                    @if(request('keyword'))
                        <a href="{{ route('role.index') }}" class="btn btn-danger btn-sm ms-2" title="Reset">
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
                        <th scope="col">Nama Peran (Role)</th>
                        {{-- Kolom Lokasi Penyimpanan Dihapus --}}
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Diperbarui ke $roles --}}
                    @forelse ($roles as $item)
                    <tr>
                        {{-- Diperbarui ke $roles --}}
                        <th scope="row">{{ ($roles->currentPage() - 1) * $roles->perPage() + $loop->iteration }}</th>
                        {{-- Diperbarui ke nama_role (Sesuai Model Role) --}}
                        <td>{{ $item->nama_role }}</td>
                        {{-- Kolom Lokasi Dihapus --}}
                        
                        <td class="align-top">
                            <div class="d-flex gap-1">
                                {{-- Diperbarui ke role.* dan id_role --}}
                                <a class="btn btn-info btn-sm" href="{{ route('role.show', $item->id_role) }}"><i class="fas fa-eye"></i></a>
                                <a class="btn btn-warning btn-sm" href="{{ route('role.edit', $item->id_role) }}"><i class="fas fa-pencil-alt"></i></a>
                                <form action="{{ route('role.destroy', $item->id_role) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    {{-- Menggunakan onsubmit (dari template) --}}
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini? (Role yang sedang dipakai user akan gagal dihapus)')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        {{-- colspan disesuaikan menjadi 3 --}}
                        <td colspan="3" class="text-center">
                            @if(request('keyword'))
                                Data dengan kata kunci "{{ request('keyword') }}" tidak ditemukan.
                            @else
                                Tidak ada data peran (role).
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- BAGIAN PAGINATION --}}
        {{-- Diperbarui ke $roles --}}
        <div class="d-flex justify-content-end mt-3">
            {{ $roles->links() }}
        </div>
    </div>
</div>
@endsection