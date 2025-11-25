@extends('layouts.app')

@section('content')
<div class="container-fluid">
  {{-- Tombol Kembali --}}
  <a href="{{ route('lamaran.index.admin') }}" class="btn btn-secondary mb-4">
    <i class="fas fa-arrow-left"></i> Kembali
  </a>

  {{-- ROW UTAMA: GRID dengan GAP --}}
  <div class="row g-4"> {{-- Gunakan g-4 untuk gap antar kolom --}}

    {{-- KOLOM KIRI (md-4): Info Lamaran & Foto Peserta --}}
    <div class="col-md-4">

      {{-- KARTU A: Informasi Lamaran (Status & Durasi) --}}
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
          {{-- Catatan Penolakan (Hanya jika ditolak) --}}
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
            {{-- Bagian Foto Profil Peserta di show.blade.php --}}
            <div class="col-md-3 text-center">

              {{-- PERBAIKAN DISINI: Ambil dari $lamaran->pengguna->foto_profil --}}
              @if($lamaran->pengguna->foto_profil)
              <img src="{{ asset('storage/foto_profil/' . $lamaran->pengguna->foto_profil) }}"
                alt="Foto Profil {{ $lamaran->pengguna->name }}"
                class="img-fluid rounded-circle shadow-sm"
                style="width: 120px; height: 120px; object-fit: cover;">
              @else
              {{-- Placeholder --}}
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
          <div class="row mb-2">
            <div class="col-md-4 fw-bold text-secondary">Alamat</div>
            <div class="col-md-8">: {{ $lamaran->pengguna->biodata->alamat ?? '-' }}</div>
          </div>

          <hr class="my-4">

          {{-- Bagian File Pendukung --}}
          <h6 class="fw-bold text-dark mb-3">Dokumen Pendukung</h6>
          <div class="row g-3">

            {{-- File CV --}}
            <div class="col-md-6">
              <div class="card h-100 shadow-sm py-2">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Curriculum Vitae (CV)</div>
                      @if(!empty($lamaran->pengguna->biodata->file_cv))
                      <a href="{{ asset('storage/' . $lamaran->pengguna->biodata->file_cv) }}" target="_blank" class="btn btn-sm btn-primary mt-1">
                        <i class="fas fa-download me-1"></i> Lihat / Unduh
                      </a>
                      @else
                      <div class="text-danger small mt-1">Tidak ada file diunggah</div>
                      @endif
                    </div>
                    <div class="col-auto"><i class="fas fa-file-pdf fa-2x text-gray-300"></i></div>
                  </div>
                </div>
              </div>
            </div>

            {{-- File Transkrip / Proposal --}}
            <div class="col-md-6">
              <div class="card h-100 shadow-sm py-2">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Transkrip / Proposal</div>
                      @if(!empty($lamaran->file_proposal))
                      <a href="{{ asset('storage/' . $lamaran->file_proposal) }}" target="_blank" class="btn btn-sm btn-info mt-1 text-white">
                        <i class="fas fa-download me-1"></i> Lihat / Unduh
                      </a>
                      @else
                      <div class="text-danger small mt-1">Tidak ada file diunggah</div>
                      @endif
                    </div>
                    <div class="col-auto"><i class="fas fa-file-alt fa-2x text-gray-300"></i></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @else
          <div class="alert alert-warning mt-3" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Pengguna ini belum melengkapi biodata profilnya (NIM/Kampus, dll).
          </div>
          @endif
        </div>
        <div class="card-footer bg-light">
          <small class="text-muted">Data ditampilkan berdasarkan input pengguna terakhir.</small>
        </div>
      </div>
    </div> {{-- End col-md-8 --}}

  </div> {{-- End row utama --}}
</div>
@endsection