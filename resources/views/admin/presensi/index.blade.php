@extends('layouts.app')

@section('content')
<div class="container-fluid">

  {{-- ============================================================== --}}
  {{-- 1. FILTER & ACTION BAR --}}
  {{-- ============================================================== --}}
  <div class="card shadow-sm mb-4 border-0 border-top border-primary border-4">
    <div class="card-body py-3">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-end gap-3">

        {{-- FORM FILTER --}}
        <form action="{{ route('admin.presensi.index') }}" method="GET" class="row g-2 align-items-end flex-grow-1 w-100">
          <div class="col-md-3">
            <label class="form-label fw-bold small text-secondary">Dari Tanggal</label>
            <input type="date" name="mulai" class="form-control form-control-sm" value="{{ $mulai }}">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold small text-secondary">Sampai Tanggal</label>
            <input type="date" name="selesai" class="form-control form-control-sm" value="{{ $selesai }}">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold small text-secondary">Status</label>
            <select name="status" class="form-select form-select-sm">
              <option value="">Semua Status</option>
              <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
              <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
              <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
              <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
            </select>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
              <i class="fas fa-filter me-1"></i> Terapkan
            </button>
          </div>
        </form>

        {{-- TOMBOL AKSI (Input & Cetak) --}}
        <div class="d-flex gap-2 w-100 w-md-auto">
          <button class="btn btn-success btn-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fas fa-plus-circle me-1"></i> Input Manual
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- ============================================================== --}}
  {{-- 2. RINGKASAN STATISTIK (DENGAN WARNA) --}}
  {{-- ============================================================== --}}
  <div class="row g-3 mb-4">

    {{-- Card Hadir --}}
    <div class="col-xl-3 col-md-6">
      <div class="card border-start border-4 border-primary shadow-sm h-100">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Hadir</div>
              <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $ringkasan['hadir'] }}</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-user-check fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Card Terlambat --}}
    <div class="col-xl-3 col-md-6">
      <div class="card border-start border-4 border-warning shadow-sm h-100">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Terlambat</div>
              <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $ringkasan['terlambat'] }}</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-clock fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Card Sakit / Izin --}}
    <div class="col-xl-3 col-md-6">
      <div class="card border-start border-4 border-info shadow-sm h-100">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sakit / Izin</div>
              <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $ringkasan['sakit_izin'] }}</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-file-medical fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Card Alpha --}}
    <div class="col-xl-3 col-md-6">
      <div class="card border-start border-4 border-danger shadow-sm h-100">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Alpha</div>
              <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $ringkasan['alpha'] }}</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-user-times fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ============================================================== --}}
  {{-- 3. TABEL DATA UTAMA --}}
  {{-- ============================================================== --}}
  <div class="card shadow-sm mb-4 border-0">
    <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
      <h6 class="m-0 font-weight-bold text-primary">
        <i class="fas fa-table me-1"></i> Data Presensi
      </h6>
      <span class="badge bg-light text-dark border">
        {{ \Carbon\Carbon::parse($mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($selesai)->format('d M Y') }}
      </span>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-secondary">
            <tr>
              <th class="ps-4" width="5%">No</th>
              <th>Tanggal</th>
              <th>Nama Peserta</th>
              <th>Jam Masuk</th>
              <th>Jam Pulang</th>
              <th>Catatan</th>
              <th class="text-center">Status</th>
              <th class="text-center" width="10%">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($presensis as $item)
            <tr>
              <td class="ps-4">{{ $loop->iteration }}</td>

              {{-- Tanggal --}}
              <td>
                <span class="fw-bold text-dark">{{ $item->tanggal->format('d/m/Y') }}</span>
                <div class="small text-muted">{{ $item->tanggal->translatedFormat('l') }}</div>
              </td>

              {{-- Info Peserta --}}
              <td>
                <div class="fw-bold text-dark">{{ $item->lamaran->pengguna->name }}</div>
                <small class="text-muted">
                  <i class="fas fa-briefcase me-1"></i>{{ $item->lamaran->lowongan->divisi }}
                </small>
              </td>
              {{-- JAM MASUK --}}
              <td>
                @if($item->jam_masuk)
                <span class="fw-bold text-success">{{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}</span>
                @if($item->status == 'terlambat')
                <span class="badge bg-danger ms-1" style="font-size: 0.6rem;">LATE</span>
                @endif
                @else
                {{-- Jika NULL (Sakit/Izin) tampilkan strip --}}
                <span class="text-muted">-</span>
                @endif
              </td>

              {{-- JAM PULANG --}}
              <td>
                @if($item->jam_pulang)
                <span class="fw-bold text-primary">{{ \Carbon\Carbon::parse($item->jam_pulang)->format('H:i') }}</span>
                @if($item->is_auto_closed)
                <i class="fas fa-robot text-warning ms-1" title="System Auto-Close"></i>
                @endif
                @else
                <span class="text-muted">-</span>
                @endif
              </td>

              {{-- Catatan --}}
              <td>
                @if($item->catatan_harian)
                {{-- Hapus class 'text-truncate' --}}
                {{-- Tambahkan style 'white-space: normal' agar teks turun ke bawah --}}
                <small class="text-muted fst-italic d-block" style="min-width: 150px; white-space: normal; line-height: 1.3;">
                  {{ $item->catatan_harian }}
                </small>
                @else
                <span class="text-muted">-</span>
                @endif
              </td>
              {{-- Status Badge --}}
              <td class="text-center">
                @php
                $badgeColor = match($item->status) {
                'hadir' => 'success',
                'sakit' => 'info',
                'izin' => 'warning text-dark',
                'alpha' => 'danger',
                default => 'secondary'
                };
                @endphp
                <span class="badge bg-{{ $badgeColor }} rounded-pill px-3">
                  {{ ucfirst($item->status) }}
                </span>
              </td>

              {{-- Tombol Aksi --}}
              <td class="text-center">
                <div class="btn-group gap-1" role="group">
                  <button class="btn btn-warning btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#editModal"
                    data-id="{{ $item->id }}"
                    data-masuk="{{ $item->jam_masuk }}"
                    data-pulang="{{ $item->jam_pulang }}"
                    data-status="{{ $item->status }}"
                    title="Edit Absen">
                    <i class="fas fa-pencil-alt"></i>
                  </button>

                  <form action="{{ route('admin.presensi.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data presensi ini?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center py-5 text-muted">
                <div class="mb-2"><i class="fas fa-folder-open fa-2x opacity-50"></i></div>
                <p class="mb-0">Tidak ada data presensi pada periode ini.</p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

{{-- INCLUDE MODALS --}}
@include('admin.presensi.partials.create_modal')
@include('admin.presensi.partials.edit_modal')

@endsection