{{-- Tab Content: Documents --}}

<div class="row g-4">

  {{-- Document 1: Surat Pengantar --}}
  <div class="col-md-6">
    <div class="card border-0 bg-light h-100">
      <div class="card-body">
        <div class="d-flex align-items-start mb-3">
          <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
            <i class="fas fa-file-pdf fa-2x text-primary"></i>
          </div>
          <div class="flex-grow-1">
            <h6 class="fw-bold mb-1">Surat Pengantar</h6>
            <small class="text-muted">
              Diunggah {{ $item->created_at->diffForHumans() }}
            </small>
          </div>
        </div>

        <div class="border-top pt-3">
          @if($item->file_surat_pengantar)
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge bg-success">
              <i class="fas fa-check-circle me-1"></i> Tersedia
            </span>
            <small class="text-muted">
              {{ $item->created_at->format('d M Y, H:i') }}
            </small>
          </div>
          <a
            href="{{ asset('storage/surat_pengantar/' . $item->file_surat_pengantar) }}"
            target="_blank"
            class="btn btn-primary btn-sm w-100">
            <i class="fas fa-eye me-2"></i>Lihat Dokumen
          </a>
          @else
          <div class="alert alert-warning mb-0">
            <i class="fas fa-exclamation-triangle me-2"></i>
            File tidak tersedia
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- Document 2: Laporan Akhir --}}
  <div class="col-md-6">
    <div class="card border-0 bg-light h-100">
      <div class="card-body">
        <div class="d-flex align-items-start mb-3">
          <div class="bg-success bg-opacity-10 rounded p-3 me-3">
            <i class="fas fa-file-alt fa-2x text-success"></i>
          </div>
          <div class="flex-grow-1">
            <h6 class="fw-bold mb-1">Laporan Akhir</h6>
            <small class="text-muted">
              @if($item->path_laporan_akhir)
              Sudah diunggah
              @else
              Belum diunggah
              @endif
            </small>
          </div>
        </div>

        <div class="border-top pt-3">
          @if($item->path_laporan_akhir)
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge bg-success">
              <i class="fas fa-check-circle me-1"></i> Tersedia
            </span>
            <small class="text-muted">
              {{ $item->updated_at->format('d M Y, H:i') }}
            </small>
          </div>
          <a
            href="{{ asset('storage/laporan_akhir/' . $item->path_laporan_akhir) }}"
            target="_blank"
            class="btn btn-success btn-sm w-100">
            <i class="fas fa-download me-2"></i>Download Laporan
          </a>
          @else
          <div class="alert alert-info mb-0">
            <i class="fas fa-clock me-2"></i>
            Menunggu peserta mengunggah laporan
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- Document 3: Surat Balasan (Optional - Jika field ada di DB) --}}
  {{-- Uncomment jika kolom path_surat_balasan sudah ada
  @if($item->path_surat_balasan ?? false)
    <div class="col-md-6">
      <div class="card border-0 bg-light h-100">
        <div class="card-body">
          <div class="d-flex align-items-start mb-3">
            <div class="bg-info bg-opacity-10 rounded p-3 me-3">
              <i class="fas fa-envelope-open-text fa-2x text-info"></i>
            </div>
            <div class="flex-grow-1">
              <h6 class="fw-bold mb-1">Surat Balasan</h6>
              <small class="text-muted">Surat penerimaan resmi</small>
            </div>
          </div>

          <div class="border-top pt-3">
            <a 
              href="{{ asset($item->path_surat_balasan) }}"
  target="_blank"
  class="btn btn-info btn-sm w-100"
  >
  <i class="fas fa-download me-2"></i>Download Surat
  </a>
</div>
</div>
</div>
</div>
@endif
--}}

</div>

{{-- Document Summary --}}
<div class="card border-0 bg-primary bg-opacity-10 mt-4">
  <div class="card-body">
    <h6 class="fw-bold text-primary mb-3">
      <i class="fas fa-info-circle me-2"></i>Informasi Dokumen
    </h6>
    <ul class="mb-0 small">
      <li class="mb-2">
        <strong>Surat Pengantar:</strong> Dokumen wajib yang harus dilampirkan saat pendaftaran
      </li>
      <li>
        <strong>Laporan Akhir:</strong> Diunggah oleh peserta menjelang masa magang berakhir
      </li>
    </ul>
  </div>
</div>