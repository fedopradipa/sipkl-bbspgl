<div class="card shadow-sm border-0">
  <div class="card-header bg-white py-3 border-bottom">
    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-graduate me-2"></i>Profil Pelamar</h6>
  </div>
  <div class="card-body">

    {{-- Header Profil --}}
    <div class="d-flex align-items-center mb-4">
      <div class="flex-shrink-0">
        @if($lamaran->pengguna->foto_profil)
        <img src="{{ asset('storage/foto_profil/' . $lamaran->pengguna->foto_profil) }}"
          alt="Foto" class="rounded-circle shadow-sm border"
          style="width: 80px; height: 80px; object-fit: cover;">
        @else
        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center border"
          style="width: 80px; height: 80px;">
          <i class="fas fa-user fa-3x text-secondary opacity-50"></i>
        </div>
        @endif
      </div>
      <div class="flex-grow-1 ms-3">
        <h4 class="fw-bold mb-0 text-dark">{{ $lamaran->pengguna->name }}</h4>
        <p class="text-muted mb-0"><i class="far fa-envelope me-1"></i> {{ $lamaran->pengguna->email }}</p>
      </div>
    </div>

    {{-- Alert Biodata --}}
    @if(!$lamaran->pengguna->biodata)
    <div class="alert alert-warning d-flex align-items-center" role="alert">
      <i class="fas fa-exclamation-triangle me-2"></i>
      <div>Pengguna ini belum melengkapi biodata profilnya.</div>
    </div>
    @else
    {{-- Grid Biodata --}}
    <div class="row g-3 mb-4">
      <div class="col-md-6">
        <div class="p-3 bg-light rounded h-100 border">
          <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Institusi / Kampus</small>
          <div class="fw-bold text-dark mt-1">{{ $lamaran->pengguna->biodata->institusi ?? '-' }}</div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="p-3 bg-light rounded h-100 border">
          <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Jurusan / Prodi</small>
          <div class="fw-bold text-dark mt-1">{{ $lamaran->pengguna->biodata->jurusan ?? '-' }}</div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="p-3 bg-light rounded h-100 border">
          <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">NIM / NIS</small>
          <div class="fw-bold text-dark mt-1">{{ $lamaran->pengguna->biodata->nim_nis ?? '-' }}</div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="p-3 bg-light rounded h-100 border">
          <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Kontak HP/WA</small>
          <div class="fw-bold text-dark mt-1">{{ $lamaran->pengguna->biodata->no_hp ?? '-' }}</div>
        </div>
      </div>
    </div>
    @endif

    <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-paperclip me-2"></i>Berkas Lampiran</h6>

    {{-- Surat Pengantar Style --}}
    <div class="card mb-3 border">
      <div class="card-body p-3 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
          <div class="bg-danger text-white rounded p-3 me-3">
            <i class="fas fa-file-pdf fa-2x"></i>
          </div>
          <div>
            <h6 class="mb-0 fw-bold text-dark">Surat Pengantar Magang</h6>
            <small class="text-muted">
              @if($lamaran->file_surat_pengantar)
              Dokumen tersedia untuk ditinjau.
              @else
              <span class="text-danger">File belum diunggah peserta.</span>
              @endif
            </small>
          </div>
        </div>

        @if($lamaran->file_surat_pengantar)
        <a href="{{ asset('storage/surat_pengantar/' . $lamaran->file_surat_pengantar) }}"
          target="_blank"
          class="btn btn-outline-primary btn-sm">
          <i class="fas fa-eye me-1"></i> Review
        </a>
        @else
        <button class="btn btn-light btn-sm text-muted" disabled>Tidak Ada File</button>
        @endif
      </div>
    </div>
  </div>
</div>