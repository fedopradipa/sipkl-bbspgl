@extends('layouts.app')

@section('content')
<div class="container-fluid">

  <h4 class="fw-bold text-primary mb-4">Rekap Absensi Bulanan Peserta</h4>

  {{-- FILTER FORM --}}
  <div class="card shadow-sm mb-4 border-0">
    <div class="card-body">
      <form action="{{ route('admin.reports.attendance.index') }}" method="GET" class="row g-3 align-items-end">

        <div class="col-md-5">
          <label for="start_date" class="form-label small text-muted">Tanggal Mulai</label>
          <input type="date" name="start_date" id="start_date" class="form-control"
            value="{{ $startDate->format('Y-m-d') }}" required>
        </div>

        <div class="col-md-5">
          <label for="end_date" class="form-label small text-muted">Tanggal Selesai</label>
          <input type="date" name="end_date" id="end_date" class="form-control"
            value="{{ $endDate->format('Y-m-d') }}" required>
        </div>

        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary w-100">Filter</button>

          {{-- Tombol Export PDF --}}
          @if($rekapAbsensi->count() > 0)
          <a href="{{ route('admin.reports.attendance.pdf', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}"
            class="btn btn-danger" title="Cetak PDF">
            <i class="fas fa-file-pdf"></i>
          </a>
          @endif
        </div>
      </form>
    </div>
  </div>

  {{-- INFORMASI REKAP HEADER (DIPERBAIKI UNTUK KEJELASAN) --}}
  <div class="alert alert-info py-2 small">
    Rekapitulasi dari tanggal <strong>{{ $startDate->format('d M Y') }}</strong> sampai <strong>{{ $endDate->format('d M Y') }}</strong>.
    Total Hari Kerja (Senin-Jumat) dalam periode filter: <strong>{{ $totalEffectiveDays }} Hari</strong>.
    <br>
    *Catatan: Hari Efektif (**Jadwal**) di kolom tabel dihitung berdasarkan **Rentang Irisan** antara periode filter dan periode magang resmi setiap peserta.
  </div>

  {{-- TABEL PREVIEW --}}
  <div class="card shadow mb-4">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th class="ps-4">Peserta & Divisi</th>
              {{-- UX UPGRADE: Menambahkan Title/Keterangan pada Kolom Hadir Total --}}
              <th class="text-center" title="Termasuk Hadir Tepat Waktu dan Terlambat">Hadir Total (H)</th>
              <th class="text-center">Sakit (S)</th>
              <th class="text-center">Izin (I)</th>
              <th class="text-center">Alpha (A)</th>
              <th class="text-center">Total Terlambat</th>
              <th class="text-center">Hari Efektif (Jadwal)</th>
              <th class="text-end pe-4">% Kehadiran</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rekapAbsensi as $rekap)

            {{-- Logic Color Coding untuk Persentase (UX UPGRADE) --}}
            @php
            $persen = (float) $rekap->persentase;
            $colorClass = match (true) {
            $persen >= 90 => 'text-success', // Hijau: Sangat Baik
            $persen >= 70 => 'text-warning', // Kuning: Cukup
            default => 'text-danger', // Merah: Buruk
            };
            @endphp

            <tr>
              <td class="ps-4">
                <div class="fw-bold">{{ $rekap->nama_peserta }}</div>
                <small class="text-muted">{{ $rekap->divisi_peserta }}</small>
              </td>
              <td class="text-center text-success fw-bold">{{ $rekap->total_hadir_terlambat }}</td>
              <td class="text-center text-info">{{ $rekap->total_sakit }}</td>
              <td class="text-center text-warning">{{ $rekap->total_izin }}</td>
              <td class="text-center text-danger">{{ $rekap->total_alpha }}</td>
              <td class="text-center">{{ $rekap->total_terlambat }}</td>

              {{-- MENGGUNAKAN HARI EFEKTIF INDIVIDUAL --}}
              <td class="text-center fw-bold">{{ $rekap->total_days }}</td>

              {{-- UX UPGRADE: Color Coding Persentase --}}
              <td class="text-end pe-4 fs-6 fw-bold {{ $colorClass }}">
                {{ $rekap->persentase }}%
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center py-5 text-muted">Tidak ada data absensi dalam periode ini.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection