@extends('layouts.app')

@section('content')
{{-- TABEL --}}
<div class="card mb-4">
  <div class="card-body">
    <h5 class="card-title">Data Pembimbing Lapangan</h5>
    {{-- Controls: Add, Filter, Search --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
      <div class="d-flex flex-wrap align-items-center">
        <a class="btn btn-primary btn-sm mb-2 me-2" href="{{ route('pembimbing.create') }}">
          <i class="fas fa-plus me-1"></i> Tambah Pembimbing
        </a>
      </div>

      <div class="col-12 col-md-auto">
        {{-- Form untuk pencarian --}}
        <form action="{{ route('pembimbing.index') }}" method="GET">
          <div class="input-group input-group-sm shadow-sm" style="max-width: 250px;">
            <input
              type="text"
              name="keyword"
              class="form-control"
              placeholder="Cari nama/jabatan..."
              value="{{ request('keyword') }}">
            <button type="submit" class="btn btn-primary" title="Cari">
              <i class="fas fa-search"></i>
            </button>
            @if(request('keyword'))
            <a href="{{ route('pembimbing.index') }}" class="btn btn-outline-secondary" title="Reset">
              <i class="fas fa-times"></i>
            </a>
            @endif
          </div>
        </form>
      </div>
    </div>

  </div>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Nama Lengkap</th>
          <th scope="col">Jabatan</th>
          <th scope="col">No. HP</th>
          <th scope="col" class="text-center">Status</th>
          <th scope="col">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($pembimbings as $item)
        <tr>
          <th scope="row">{{ ($pembimbings->currentPage() - 1) * $pembimbings->perPage() + $loop->iteration }}</th>
          <td class="fw-bold">{{ $item->nama }}</td>
          <td>{{ $item->jabatan }}</td>
          <td>
            @if($item->no_hp)
            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $item->no_hp)) }}" target="_blank" class="text-decoration-none">
              <i class="fab fa-whatsapp text-success me-1"></i> {{ $item->no_hp }}
            </a>
            @else
            <span class="text-muted">-</span>
            @endif
          </td>
          <td class="text-center">
            @if($item->is_active)
            <span class="badge bg-success">Aktif</span>
            @else
            <span class="badge bg-secondary">Non-Aktif</span>
            @endif
          </td>
          <td class="align-top">
            <div class="d-flex gap-1">
              <a class="btn btn-warning btn-sm" href="{{ route('pembimbing.edit', $item->id) }}" title="Edit"><i class="fas fa-pencil-alt"></i></a>
              <form action="{{ route('pembimbing.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center">
            @if(request('keyword'))
            Pembimbing dengan kata kunci "{{ request('keyword') }}" tidak ditemukan.
            @else
            Tidak ada data pembimbing.
            @endif
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- BAGIAN PAGINATION --}}
  <div class="d-flex justify-content-end mt-3">
    {{ $pembimbings->links() }}
  </div>
</div>
</div>
@endsection