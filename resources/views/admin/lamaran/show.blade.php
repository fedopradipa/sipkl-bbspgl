@extends('layouts.app')

@section('content')
<div class="container-fluid">

  {{-- 1. DATA PREPARATION BLOCK (Best Practice Clean-up) --}}
  @php
  // Persiapan data atribut untuk Modal (mengurangi redundansi di tombol)
  $resmiStart = $lamaran->tgl_mulai_resmi ? $lamaran->tgl_mulai_resmi->format('Y-m-d') : $lamaran->tgl_mulai_usulan->format('Y-m-d');
  $resmiEnd = $lamaran->tgl_selesai_resmi ? $lamaran->tgl_selesai_resmi->format('Y-m-d') : $lamaran->tgl_selesai_usulan->format('Y-m-d');
  $resmiDays = $lamaran->hari_kerja_resmi ? json_encode($lamaran->hari_kerja_resmi) : json_encode($lamaran->hari_kerja_usulan);
  $pembimbingId = $lamaran->pembimbing_id ?? '';

  // Kumpulan semua atribut data untuk disebar (spread) di tombol
  $modalDataAttributes = [
  'lamaran-id' => $lamaran->id,
  'current-status' => $lamaran->status,
  'usulan-start' => $lamaran->tgl_mulai_usulan->format('Y-m-d'),
  'resmi-pembimbing' => $pembimbingId,
  'resmi-start' => $resmiStart,
  'resmi-end' => $resmiEnd,
  'resmi-days' => $resmiDays,
  ];
  @endphp

  {{-- ROW UTAMA: GRID dengan GAP --}}
  <div class="row g-4 mb-5 pb-5"> {{-- Tambahkan margin bottom untuk Fixed Footer --}}

    {{-- KOLOM KIRI (md-4): Info Lamaran & Penerimaan Resmi --}}
    <div class="col-md-4">

      {{-- KARTU A: Info Lamaran --}}
      <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
          <h6 class="m-0 font-weight-bold">Info Lamaran</h6>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="small text-muted">Posisi Dilamar</label>
            <div class="fw-bold">{{ $lamaran->lowongan->judul }}</div>
            <small class="text-secondary">{{ $lamaran->lowongan->divisi }}</small>
          </div>
          <div class="mb-3">
            <label class="small text-muted">Tanggal Melamar</label>
            <div>{{ $lamaran->created_at->format('d F Y, H:i') }}</div>
          </div>
          <div class="mb-3">
            <label class="small text-muted">Durasi Usulan</label>
            <div>{{ $lamaran->tgl_mulai_usulan->format('d M Y') }} - {{ $lamaran->tgl_selesai_usulan->format('d M Y') }}</div>
          </div>
          <div class="mb-3">
            <label class="small text-muted">Hari Kerja Usulan</label>
            <div>
              @if(is_array($lamaran->hari_kerja_usulan))
              @foreach($lamaran->hari_kerja_usulan as $day)
              <span class="badge bg-secondary me-1">{{ $day }}</span>
              @endforeach
              @else
              <span class="text-muted">-</span>
              @endif
            </div>
          </div>
          <div class="mb-3">
            <label class="small text-muted">Status</label> <br>
            <span class="badge bg-{{ $lamaran->status === 'pending' ? 'warning text-dark' : ($lamaran->status === 'diterima' ? 'success' : 'danger') }} fs-6 px-3 py-2">
              {{ ucfirst($lamaran->status) }}
            </span>
          </div>
          @if($lamaran->status == 'ditolak')
          <div class="alert alert-danger small mt-3 p-2">
            <strong>Catatan Admin:</strong> {{ $lamaran->catatan_admin ?? 'Tidak ada catatan.' }}
          </div>
          @endif
        </div>
      </div>

      {{-- KARTU B: Data Penerimaan Resmi (Jika sudah diterima) --}}
      @if($lamaran->status == 'diterima')
      <div class="card shadow border-success border-3 mt-4">
        <div class="card-header py-3 fw-bold bg-success text-white">Data Penerimaan Resmi</div>
        <div class="card-body">
          <h5 class="fw-bold text-dark">Pembimbing Lapangan</h5>
          <p class="fs-5 mb-1">{{ $lamaran->pembimbing->nama ?? '—' }}</p>
          <p class="text-muted small">{{ $lamaran->pembimbing->jabatan ?? 'Belum Ditetapkan' }}</p>

          <h5 class="fw-bold mt-4 text-dark">Jadwal Resmi</h5>
          <p class="mb-1"><strong>Mulai Efektif:</strong> <span class="text-success fw-bold">{{ $lamaran->tgl_mulai_resmi->format('d M Y') }}</span></p>
          <p><strong>Berakhir:</strong> <span class="text-danger fw-bold">{{ $lamaran->tgl_selesai_resmi->format('d M Y') }}</span></p>

          <h5 class="fw-bold mt-4 text-dark">Hari Kerja Resmi</h5>
          <div class="d-flex flex-wrap gap-2">
            @foreach($lamaran->hari_kerja_resmi as $day)
            <span class="badge bg-primary fs-6">{{ $day }}</span>
            @endforeach
          </div>
        </div>
        <div class="card-footer text-center bg-light">
          @if($lamaran->path_surat_balasan)
          <a href="{{ asset($lamaran->path_surat_balasan) }}" target="_blank" class="btn btn-sm btn-primary">
            <i class="fas fa-download me-1"></i> Download Surat Balasan
          </a>
          @else
          <span class="text-muted small">Surat balasan belum dibuat.</span>
          @endif
        </div>
      </div>
      @endif

    </div> {{-- End col-md-4 --}}

    {{-- KOLOM KANAN (md-8): Profil Peserta & Dokumen --}}
    <div class="col-md-8">
      <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
          <h6 class="m-0 font-weight-bold">Profil Peserta & Dokumen</h6>
        </div>
        <div class="card-body">

          <div class="row align-items-center mb-4">
            {{-- Foto Profil --}}
            <div class="col-md-3 text-center">
              @if($lamaran->pengguna->foto_profil)
              <img src="{{ asset('storage/foto_profil/' . $lamaran->pengguna->foto_profil) }}"
                alt="Foto Profil {{ $lamaran->pengguna->name }}"
                class="img-fluid rounded-circle shadow-sm"
                style="width: 120px; height: 120px; object-fit: cover;">
              @else
              <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px;">
                <i class="fas fa-user-circle fa-4x text-muted"></i>
              </div>
              @endif
            </div>
            {{-- Nama & Email --}}
            <div class="col-md-9">
              <h4 class="mb-1 fw-bold">{{ $lamaran->pengguna->name }}</h4>
              <p class="text-muted">{{ $lamaran->pengguna->email }}</p>
            </div>
          </div>

          <hr class="my-4">

          {{-- Data Biodata Tambahan --}}
          @if($lamaran->pengguna->biodata)
          <h6 class="fw-bold text-dark mb-3">Detail Kontak & Institusi</h6>
          <div class="row mb-2">
            <div class="col-md-4 fw-bold text-secondary">NIM / NIS</div>
            <div class="col-md-8">: {{ $lamaran->pengguna->biodata->nim_nis ?? '-' }}</div>
          </div>
          <div class="row mb-2">
            <div class="col-md-4 fw-bold text-secondary">Asal Sekolah/Kampus</div>
            <div class="col-md-8">: {{ $lamaran->pengguna->biodata->institusi ?? '-' }}</div>
          </div>
          <div class="row mb-2">
            <div class="col-md-4 fw-bold text-secondary">Jurusan</div>
            <div class="col-md-8">: {{ $lamaran->pengguna->biodata->jurusan ?? '-' }}</div>
          </div>
          <div class="row mb-2">
            <div class="col-md-4 fw-bold text-secondary">Nomor HP/WA</div>
            <div class="col-md-8">: {{ $lamaran->pengguna->biodata->no_hp ?? '-' }}</div>
          </div>

          <hr class="my-4">


          {{-- File Surat Pengantar --}}
          <div class="col-md-6">
            <div class="card h-100 shadow-sm py-2">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Surat Pengantar</div>

                    @if($lamaran->file_surat_pengantar)
                    {{-- Tombol Download --}}
                    <a href="{{ asset('storage/surat_pengantar/' . $lamaran->file_surat_pengantar) }}"
                      target="_blank"
                      class="btn btn-sm btn-primary mt-1">
                      <i class="fas fa-download me-1"></i> Lihat / Unduh
                    </a>
                    @else
                    <div class="text-danger small mt-1">File tidak dilampirkan</div>
                    @endif
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-envelope-open-text fa-2x text-gray-300"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>


          @else
          <div class="alert alert-warning mt-3" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Pengguna ini belum melengkapi biodata profilnya.
          </div>
          @endif
        </div>
        <div class="card-footer bg-light">
          <small class="text-muted">Data ditampilkan berdasarkan input pengguna terakhir.</small>
        </div>
      </div>
    </div> {{-- End col-md-8 --}}
  </div> {{-- End row utama --}}

  {{-- 2. ACTION BAR (Fixed Bottom) --}}
  <div class="fixed-bottom bg-white shadow-lg border-top py-3">
    <div class="container-fluid d-flex justify-content-between align-items-center">

      {{-- Tombol Kembali --}}
      <a href="{{ route('lamaran.index.admin') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Verifikasi
      </a>

      {{-- Tombol Aksi --}}
      <div class="d-flex gap-2">

        {{-- TOMBOL HAPUS --}}
        <form action="{{ route('lamaran.destroy', $lamaran->id) }}" method="POST" onsubmit="return confirm('Hapus permanen data ini? Peserta akan hilang dari sistem.');">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger ">
            <i class="fas fa-trash me-1"></i> Hapus Permanen
          </button>
        </form>

        {{-- LOGIKA UTAMA --}}
        @if($lamaran->status == 'pending')
        {{-- Hanya muncul jika PENDING --}}
        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal" data-lamaran-id="{{ $lamaran->id }}">
          <i class="fas fa-times me-1"></i> Tolak Lamaran
        </button>
        <button class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#approveModal"
          @foreach($modalDataAttributes as $key=> $value)
          data-{{ $key }}="{{ $value }}"
          @endforeach
          >
          <i class="fas fa-check me-1"></i> Terima Peserta
        </button>

        @elseif($lamaran->status == 'diterima')
        {{-- Hanya muncul jika DITERIMA (Edit/Finalisasi) --}}
        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal" data-lamaran-id="{{ $lamaran->id }}">
          <i class="fas fa-times me-1"></i> Tolak Lamaran
        </button>
        <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#approveModal"
          @foreach($modalDataAttributes as $key=> $value)
          data-{{ $key }}="{{ $value }}"
          @endforeach
          >
          <i class="fas fa-edit me-1"></i> Edit Data Resmi
        </button>

        @elseif($lamaran->status == 'ditolak')
        {{-- Hanya muncul jika DITOLAK (Re-evaluasi) --}}
        <button class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#approveModal"
          @foreach($modalDataAttributes as $key=> $value)
          data-{{ $key }}="{{ $value }}"
          @endforeach
          >
          <i class="fas fa-redo me-1"></i> Terima Ulang
        </button>
        @endif
      </div>
    </div>
  </div>

  {{-- Memanggil Modal Persetujuan dan Penolakan --}}
  @include('admin.lamaran.partials.approve_modal')
  @include('admin.lamaran.partials.reject_modal')

  @endsection