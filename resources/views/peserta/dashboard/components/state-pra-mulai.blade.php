{{-- CASE 3: PRA-MULAI (Diterima, tapi belum tanggalnya) --}}
<div class="card border-0 shadow-sm mb-4 border-start border-primary border-5">
  <div class="card-body p-4">
    <h4 class="fw-bold text-primary mb-2">Selamat! Anda Diterima 🎉</h4>
    <p class="text-muted mb-4">
      Masa PKL Anda belum dimulai. Silakan persiapkan diri.
    </p>

    {{-- Tanggal Mulai Resmi --}}
    <div class="alert alert-light border d-flex align-items-center mb-4" role="alert">
      <i class="fas fa-calendar-alt text-primary me-3 fa-lg"></i>
      <div>
        <small class="text-muted d-block">Tanggal Mulai Resmi</small>
        <strong class="text-dark">{{ \Carbon\Carbon::parse($lamaran->tgl_mulai_resmi)->format('d F Y') }}</strong>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-md-6">
        <div class="p-3 bg-light rounded border">
          <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">
            Posisi Pembimbing
          </small>
          <span class="fs-5 fw-bold text-dark">
            {{ $lamaran->pembimbing->jabatan ?? 'Belum Ditentukan' }}
          </span>
        </div>
      </div>
      <div class="col-md-6">
        <div class="p-3 bg-light rounded border">
          <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem;">
            Nama Pembimbing
          </small>
          <span class="fs-5 fw-bold text-dark">
            {{ $lamaran->pembimbing->nama ?? '—' }}
          </span>
        </div>
      </div>
    </div>

    {{-- >>> BLOK TAMBAHAN: KONTAK PEMBIMBING <<< --}}
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