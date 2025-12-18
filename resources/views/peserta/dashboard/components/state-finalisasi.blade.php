<div class="row justify-content-center">
  <div class="col-lg-10">

    {{-- Alert Info --}}
    <div class="alert alert-warning shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
      <div class="bg-warning text-white rounded p-3 me-3">
        <i class="fas fa-clock fa-2x"></i>
      </div>
      <div>
        <h5 class="alert-heading fw-bold mb-1">Masa Magang Telah Berakhir</h5>
        <p class="mb-0 small">
          Periode magang Anda berakhir pada <strong>{{ \Carbon\Carbon::parse($lamaran->tgl_selesai_resmi)->translatedFormat('d F Y') }}</strong>.
          Silakan unggah Laporan Akhir untuk diproses kelulusannya oleh Admin.
        </p>
      </div>
    </div>

    <div class="row">
      {{-- KIRI: Form Upload --}}
      <div class="col-md-7 mb-4">
        <div class="card shadow hover-up border-0 h-100">
          <div class="card-header bg-white py-3 border-bottom-0">
            <h6 class="m-0 font-weight-bold text-primary">
              <i class="fas fa-file-upload me-2"></i>Unggah Laporan Akhir
            </h6>
          </div>
          <div class="card-body">

            @if($lamaran->path_laporan_akhir)
            {{-- Jika sudah upload tapi belum diluluskan --}}
            <div class="text-center py-4">
              <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
              <h5 class="fw-bold">Laporan Berhasil Diunggah!</h5>
              <p class="text-muted">
                File: <a href="{{ asset('storage/laporan_akhir/' . $lamaran->path_laporan_akhir) }}" target="_blank">{{ $lamaran->path_laporan_akhir }}</a>
              </p>
              <div class="alert alert-info small d-inline-block">
                <i class="fas fa-info-circle me-1"></i>
                Mohon tunggu Admin memverifikasi laporan Anda untuk mengubah status menjadi <strong>LULUS</strong>.
              </div>
              <hr>
              <p class="small text-muted mb-2">Ingin merevisi file?</p>
              <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#formRevisi">
                Upload Ulang Laporan
              </button>
            </div>
            @else
            {{-- Jika belum upload --}}
            <p class="text-muted small mb-4">
              Wajib mengunggah dokumen laporan akhir (PDF) sebagai syarat kelulusan dan pengambilan sertifikat.
            </p>
            @endif

            {{-- Form Upload (Muncul jika belum upload atau klik revisi) --}}
            <div class="{{ $lamaran->path_laporan_akhir ? 'collapse mt-3' : '' }}" id="formRevisi">
              <form action="{{ route('peserta.lamaran.uploadLaporan') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $lamaran->id }}">

                <div class="mb-3">
                  <label for="file_laporan" class="form-label small fw-bold">File Laporan (PDF, Max 5MB)</label>
                  <input type="file" class="form-control" name="file_laporan" id="file_laporan" accept=".pdf" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold">
                  <i class="fas fa-paper-plane me-1"></i> Kirim Laporan Akhir
                </button>
              </form>
            </div>

          </div>
        </div>
      </div>

      {{-- KANAN: Checklist & Info --}}
      <div class="col-md-5 mb-4">
        <div class="card shadow border-0 mb-3">

        </div>

        <div class="card bg-gradient-primary text-white border-0 shadow">
          <div class="card-body">
            <h6 class="fw-bold"><i class="fas fa-headset me-2"></i>Butuh Bantuan?</h6>
            <p class="small mb-2 op-8">
              Jika Anda mengalami kendala dalam penyusunan laporan atau administrasi, hubungi pembimbing.
            </p>
            <a href="https://wa.me/{{ $lamaran->pembimbing->no_hp ?? '' }}" target="_blank" class="btn btn-light btn-sm text-primary fw-bold w-100">
              <i class="fab fa-whatsapp me-1"></i> Hubungi Pembimbing
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>