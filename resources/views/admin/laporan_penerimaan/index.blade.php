@extends('layouts.app')

@section('content')
<div class="container-fluid">

  <h1 class="h3 mb-4 text-gray-800">Laporan Penerimaan Peserta PKL</h1>

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 fw-bold text-primary">Filter Data Penerimaan</h6>
    </div>
    <div class="card-body">

      {{-- FORM FILTER --}}
      <form method="GET" action="{{ route('admin.laporan.penerimaan.index') }}" class="row g-3 mb-4 align-items-end">
        <div class="col-md-3">
          <label class="form-label fw-bold">Bulan Mulai</label>
          <select name="bulan" class="form-select">
            @for($i=1; $i<=12; $i++)
              <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
              {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
              </option>
              @endfor
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label fw-bold">Tahun</label>
          <select name="tahun" class="form-select">
            @for($y=date('Y'); $y>=date('Y')-3; $y--)
            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
          </select>
        </div>
        <div class="col-md-4">
          <button type="submit" class="btn btn-primary me-2">
            <i class="fas fa-filter me-1"></i> Tampilkan
          </button>
          {{-- Tombol Cetak membawa parameter filter --}}
          <a href="{{ route('admin.laporan.penerimaan.cetak', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
            target="_blank" class="btn btn-danger">
            <i class="fas fa-print me-1"></i> Cetak Laporan
          </a>
        </div>
      </form>

      {{-- TABEL PREVIEW --}}
      <div class="table-responsive">
        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
          <thead class="table-dark">
            <tr>
              <th width="5%" class="text-center">No</th>
              <th>Nama Peserta</th>
              <th>Posisi</th>
              <th>Pembimbing</th>
              <th>Periode Resmi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($laporan as $key => $data)
            <tr>
              <td class="text-center">{{ $key + 1 }}</td>
              <td>
                <span class="fw-bold">{{ $data->pengguna->name ?? $data->pengguna->nama }}</span>
                <br>

                <div class="text-muted small mt-1">{{ $data->pengguna->email }}</div>
              </td>
              <td>
                {{-- Gunakan optional() untuk jaga-jaga jika lowongan sudah dihapus permanen --}}
                <strong>{{ optional($data->lowongan)->posisi ?? 'Posisi Tidak Ditemukan' }}</strong>
                <br>
                <span class="badge bg-secondary">{{ optional($data->lowongan)->divisi ?? '' }}</span>
              </td>
              <td>{{ $data->pembimbing->nama ?? '-' }}</td>
              <td>
                <small>
                  Mulai: {{ \Carbon\Carbon::parse($data->tgl_mulai_resmi)->translatedFormat('d M Y') }} <br>
                  Selesai: {{ \Carbon\Carbon::parse($data->tgl_selesai_resmi)->translatedFormat('d M Y') }}
                </small>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center py-4 text-muted">
                Tidak ada data penerimaan pada periode ini.
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection