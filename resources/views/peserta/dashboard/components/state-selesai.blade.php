<div class="row justify-content-center">
  <div class="col-md-10">

    {{-- CARD UTAMA: KELULUSAN --}}
    <div class="card border-0 shadow-lg border-top border-success border-5 mb-4">
      <div class="card-body text-center p-5">

        {{-- Animasi Icon / Gambar --}}
        <div class="mb-4">
          <div class="position-relative d-inline-block">
            <i class="fas fa-certificate fa-5x text-warning drop-shadow"></i>
            <i class="fas fa-check-circle fa-2x text-success position-absolute bottom-0 end-0 border border-4 border-white rounded-circle"></i>
          </div>
        </div>

        <h2 class="fw-bold text-dark mb-2">Selamat, Anda Lulus!</h2>
        <p class="text-muted mb-4 col-md-8 mx-auto">
          Terima kasih telah menyelesaikan program Praktik Kerja Lapangan (PKL) di
          <strong>BBSPGL</strong> dengan sangat baik.
        </p>

        {{-- Tombol Aksi --}}
        <div class="d-flex justify-content-center gap-3 flex-wrap">

          {{-- 1. DOWNLOAD SERTIFIKAT --}}
          {{-- Pastikan route 'sertifikat.download' sudah dibuat --}}
          <a href="{{-- route('sertifikat.download', $lamaran->id) --}}" class="btn btn-success px-4 py-2 fw-bold shadow-sm hover-scale">
            <i class="fas fa-file-download me-2"></i> Download E-Sertifikat
          </a>

          {{-- 2. DOWNLOAD TRANSKRIP NILAI (Opsional) --}}
          <a href="#" class="btn btn-outline-primary px-4 py-2 fw-bold shadow-sm hover-scale">
            <i class="fas fa-star me-2"></i> Transkrip Nilai
          </a>
        </div>
      </div>
    </div>

    {{-- CARD KEDUA: RINGKASAN DATA (ARSIP) --}}
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white fw-bold py-3">
        <i class="fas fa-archive me-2 text-secondary"></i> Arsip Dokumen Anda
      </div>
      <div class="list-group list-group-flush">

        {{-- Link ke Laporan Akhir yang sudah diupload --}}
        @if($lamaran->path_laporan_akhir)
        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
          <div>
            <div class="fw-bold text-dark">Laporan Akhir Magang</div>
            <div class="small text-muted">Diverifikasi pada {{ $lamaran->updated_at->format('d M Y') }}</div>
          </div>
          <a href="{{ asset('storage/laporan_akhir/' . $lamaran->path_laporan_akhir) }}" target="_blank" class="btn btn-sm btn-light border">
            <i class="fas fa-eye me-1"></i> Lihat
          </a>
        </div>
        @endif

        {{-- Link ke Riwayat Absensi --}}
        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
          <div>
            <div class="fw-bold text-dark">Riwayat Kehadiran</div>
            <div class="small text-muted">Rekapitulasi seluruh absensi selama magang.</div>
          </div>
          <a href="{{ route('presensi.riwayat') }}" class="btn btn-sm btn-light border">
            <i class="fas fa-list-alt me-1"></i> Buka
          </a>
        </div>

      </div>
    </div>

  </div>
</div>