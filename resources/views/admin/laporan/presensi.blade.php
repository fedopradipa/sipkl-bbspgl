@extends('layouts.app')

@section('content')
<div class="container-fluid">

  {{-- FILTER BAR --}}
  <div class="card shadow mb-4">
    <div class="card-body py-3">
      <form action="{{ route('laporan.presensi') }}" method="GET" class="row align-items-end g-3">
        <div class="col-md-4">
          <label class="form-label fw-bold">Pilih Tanggal</label>
          <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-bold">Status Kehadiran</label>
          <select name="status" class="form-select">
            <option value="">Semua Status</option>
            <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
            <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
            <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">
            <i class="fas fa-filter me-1"></i> Tampilkan
          </button>
        </div>
        <div class="col-md-3 text-end">
          {{-- Tombol Export --}}
          <button type="button" class="btn btn-success w-100" onclick="window.print()">
            <i class="fas fa-print me-1"></i> Cetak Laporan
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- RINGKASAN STATISTIK --}}
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card border-left-primary shadow h-100 py-2 border-0 border-start border-4 border-primary bg-white">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Hadir Hari Ini</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ringkasan['hadir'] }}</div>
            </div>
            <div class="col-auto"><i class="fas fa-user-check fa-2x text-gray-300"></i></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-left-warning shadow h-100 py-2 border-0 border-start border-4 border-warning bg-white">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Terlambat</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ringkasan['terlambat'] }}</div>
            </div>
            <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-left-info shadow h-100 py-2 border-0 border-start border-4 border-info bg-white">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sakit / Izin</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ringkasan['sakit_izin'] }}</div>
            </div>
            <div class="col-auto"><i class="fas fa-file-medical fa-2x text-gray-300"></i></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-left-danger shadow h-100 py-2 border-0 border-start border-4 border-danger bg-white">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Alpha / Bolos</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ringkasan['alpha'] }}</div>
            </div>
            <div class="col-auto"><i class="fas fa-times-circle fa-2x text-gray-300"></i></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- TABEL DATA --}}
  <div class="card shadow mb-4">
    <div class="card-header py-3 bg-white">
      <h6 class="m-0 font-weight-bold text-primary">Detail Presensi: {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th width="5%">No</th>
              <th>Nama Peserta</th>
              <th>Posisi</th>
              <th>Jam Masuk</th>
              <th>Jam Pulang</th>
              {{-- Kolom Lokasi DIHAPUS --}}
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($presensis as $item)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>
                <div class="fw-bold">{{ $item->lamaran->pengguna->name }}</div>
                <small class="text-muted">{{ $item->lamaran->pengguna->biodata->institusi ?? '-' }}</small>
              </td>
              <td>{{ $item->lamaran->lowongan->divisi }}</td>

              {{-- Jam Masuk --}}
              <td>
                <span class="fw-bold">{{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}</span>
                @if($item->keterangan_waktu == 'terlambat')
                <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">LATE</span>
                @endif
              </td>

              {{-- Jam Pulang --}}
              <td>
                @if($item->jam_pulang)
                {{ \Carbon\Carbon::parse($item->jam_pulang)->format('H:i') }}
                @if($item->is_auto_closed)
                <i class="fas fa-robot text-warning" title="Auto-Close System"></i>
                @endif
                @else
                <span class="badge bg-secondary">Belum Pulang</span>
                @endif
              </td>

              {{-- Kolom TD Lokasi DIHAPUS --}}

              {{-- Status --}}
              <td class="text-center">
                @if($item->status == 'hadir')
                <span class="badge bg-success">Hadir</span>
                @elseif($item->status == 'sakit')
                <span class="badge bg-info">Sakit</span>
                @elseif($item->status == 'izin')
                <span class="badge bg-warning text-dark">Izin</span>
                @else
                <span class="badge bg-danger">Alpha</span>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              {{-- Colspan disesuaikan jadi 6 karena kolom lokasi hilang --}}
              <td colspan="6" class="text-center py-4 text-muted">
                <i class="fas fa-calendar-times fa-2x mb-2"></i><br>
                Tidak ada data presensi pada tanggal ini.
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