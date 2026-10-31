{{-- CASE 3: PRA-MULAI (Diterima, tapi belum tanggalnya) --}}
<div class="card border-0 shadow-sm mb-4 border-start border-primary border-5">
  <div class="card-body p-4">
    {{-- HEADER: SELAMAT --}}
    <div class="text-center mb-4">
      <h4 class="fw-bold text-primary mb-2">Selamat! Anda Diterima 🎉</h4>
      <p class="text-muted">
        Masa PKL Anda belum dimulai. Silakan persiapkan diri sebelum tanggal resmi.
      </p>
    </div>

    {{-- INFO 1: posisi & POSISI (Highligth) --}}
    <div class="alert alert-primary bg-primary bg-opacity-10 border-0 d-flex align-items-center justify-content-between mb-4">
      <div>
        <small class="text-primary text-uppercase fw-bold" style="font-size: 0.7rem;">Posisi / Lowongan</small>
        <h5 class="fw-bold text-dark m-0">{{ $lamaran->lowongan->posisi }}</h5>
      </div>
      <div class="text-end">
        <small class="text-primary text-uppercase fw-bold" style="font-size: 0.7rem;">posisi</small>
        <h6 class="fw-bold text-dark m-0">{{ $lamaran->lowongan->posisi }}</h6>
      </div>
    </div>

    <div class="row g-3">
      {{-- INFO 2: PERIODE MAGANG --}}
      <div class="col-md-6">
        <div class="p-3 bg-light rounded border h-100">
          <small class="text-muted d-block text-uppercase fw-bold mb-2" style="font-size: 0.7rem;">
            <i class="fas fa-calendar-week me-1"></i> Periode Pelaksanaan
          </small>
          <div class="d-flex flex-column gap-1">
            <div class="d-flex justify-content-between">
              <span class="text-muted small">Mulai:</span>
              <strong class="text-success">{{ \Carbon\Carbon::parse($lamaran->tgl_mulai_resmi)->translatedFormat('d M Y') }}</strong>
            </div>
            <div class="d-flex justify-content-between border-top pt-1">
              <span class="text-muted small">Selesai:</span>
              <strong class="text-danger">{{ \Carbon\Carbon::parse($lamaran->tgl_selesai_resmi)->translatedFormat('d M Y') }}</strong>
            </div>
          </div>
        </div>
      </div>

      {{-- INFO 3: HARI KERJA RESMI --}}
      <div class="col-md-6">
        <div class="p-3 bg-light rounded border h-100">
          <small class="text-muted d-block text-uppercase fw-bold mb-2" style="font-size: 0.7rem;">
            <i class="fas fa-business-time me-1"></i> Hari Kerja Wajib
          </small>
          <div>
            @if(!empty($lamaran->hari_kerja_resmi) && is_array($lamaran->hari_kerja_resmi))
            @foreach($lamaran->hari_kerja_resmi as $hari)
            <span class="badge bg-white text-dark border shadow-sm me-1 mb-1">{{ $hari }}</span>
            @endforeach
            @else
            <span class="text-muted small fst-italic">Sesuai kebijakan kantor</span>
            @endif
          </div>
        </div>
      </div>

      {{-- INFO 4: PEMBIMBING --}}
      <div class="col-md-6">
        <div class="p-3 bg-white rounded border h-100">
          <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">
            Posisi Pembimbing
          </small>
          <span class="fs-6 fw-bold text-dark">
            {{ $lamaran->pembimbing->jabatan ?? 'Belum Ditentukan' }}
          </span>
        </div>
      </div>
      <div class="col-md-6">
        <div class="p-3 bg-white rounded border h-100">
          <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">
            Nama Pembimbing
          </small>
          <span class="fs-6 fw-bold text-dark">
            {{ $lamaran->pembimbing->nama ?? '—' }}
          </span>
        </div>
      </div>
    </div>

    {{-- KONTAK PEMBIMBING --}}
    @if($lamaran->pembimbing && $lamaran->pembimbing->no_hp)
    <h6 class="fw-bold mt-4 text-secondary border-bottom pb-2">Kontak Lanjut</h6>
    <div class="d-flex align-items-center bg-success bg-opacity-10 p-3 rounded border shadow-sm">
      <div class="bg-success p-2 rounded-circle me-3 text-white">
        <i class="fas fa-phone-volume fa-lg"></i>
      </div>
      <div>
        <h6 class="mb-0 fw-bold text-dark">{{ $lamaran->pembimbing->nama }}</h6>
        <a href="https://wa.me/{{  preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $lamaran->pembimbing->no_hp)) }}"
          target="_blank"
          class="d-block text-success mt-1 fw-bold small text-decoration-none">
          <i class="fab fa-whatsapp me-1"></i>
          {{ $lamaran->pembimbing->no_hp }} (Hubungi via WhatsApp)
        </a>
      </div>
    </div>
    @endif
  </div>
</div>