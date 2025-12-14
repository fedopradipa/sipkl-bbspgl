{{--
  Profile Card Component
  Props: $lamaran (object)
--}}

<div class="card shadow mb-4">
  <div class="card-header py-3 bg-primary text-white">
    <h6 class="m-0 font-weight-bold">Profil Peserta & Dokumen</h6>
  </div>
  <div class="card-body">

    {{-- Foto & Nama --}}
    <div class="row align-items-center mb-4">
      <div class="col-md-3 text-center">
        @if($lamaran->pengguna->foto_profil)
        <img
          src="{{ asset('storage/foto_profil/' . $lamaran->pengguna->foto_profil) }}"
          alt="Foto Profil {{ $lamaran->pengguna->name }}"
          class="img-fluid rounded-circle shadow-sm"
          style="width: 120px; height: 120px; object-fit: cover;">
        @else
        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto"
          style="width: 120px; height: 120px;">
          <i class="fas fa-user-circle fa-4x text-muted"></i>
        </div>
        @endif
      </div>
      <div class="col-md-9">
        <h4 class="mb-1 fw-bold">{{ $lamaran->pengguna->name }}</h4>
        <p class="text-muted">{{ $lamaran->pengguna->email }}</p>
      </div>
    </div>

    <hr class="my-4">

    {{-- Biodata --}}
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

    {{-- Surat Pengantar --}}
    <div class="col-md-6">
      <div class="card h-100 shadow-sm py-2">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                Surat Pengantar
              </div>

              @if($lamaran->file_surat_pengantar)
              <a
                href="{{ asset('storage/surat_pengantar/' . $lamaran->file_surat_pengantar) }}"
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