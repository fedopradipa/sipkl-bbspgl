{{-- Profile Card --}}
<div class="card border-0 shadow-sm sticky-top" style="top: 1rem;">

  {{-- Profile Header --}}
  <div class="card-body text-center p-4">
    @php
    $filename = $item->pengguna->profile->foto_profil ?? null;
    if ($filename) {
    $fotoUrl = asset('storage/foto_profil/' . $filename);
    } else {
    $fotoUrl = 'https://ui-avatars.com/api/?name='.urlencode($item->pengguna->name).'&background=4e73df&color=fff&size=200';
    }
    @endphp

    {{-- Avatar --}}
    <div class="position-relative d-inline-block mb-3">
      <img
        src="{{ $fotoUrl }}"
        class="rounded-circle border border-3 border-primary shadow-sm"
        width="120"
        height="120"
        style="object-fit: cover;"
        alt="{{ $item->pengguna->name }}">
      <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-success" style="font-size: 0.7rem;">
        <i class="fas fa-check"></i> Aktif
      </span>
    </div>

    {{-- Name & NIM --}}
    <h5 class="fw-bold mb-1">{{ $item->pengguna->name }}</h5>
    <p class="text-muted mb-3">
      <i class="fas fa-id-card me-1"></i>
      {{ $item->pengguna->profile->nim_nis ?? 'NIM Tidak Tersedia' }}
    </p>

    {{-- Contact Buttons --}}
    <div class="d-grid gap-2">
      @if($item->pengguna->profile && $item->pengguna->profile->no_hp)
      <a
        href="https://wa.me/{{ preg_replace('/^0/', '62', $item->pengguna->profile->no_hp) }}"
        target="_blank"
        class="btn btn-success btn-sm">
        <i class="fab fa-whatsapp me-2"></i>Chat WhatsApp
      </a>
      @endif

      <a href="mailto:{{ $item->pengguna->email }}" class="btn btn-outline-primary btn-sm">
        <i class="fas fa-envelope me-2"></i>{{ $item->pengguna->email }}
      </a>
    </div>
  </div>

  {{-- Education Info --}}
  <div class="card-footer bg-light border-0 p-3">
    <h6 class="text-uppercase fw-bold text-muted mb-3" style="font-size: 0.75rem;">
      <i class="fas fa-graduation-cap me-2"></i>Informasi Pendidikan
    </h6>

    <div class="mb-3">
      <div class="info-label">Institusi</div>
      <div class="info-value">
        {{ $item->pengguna->profile->institusi ?? '-' }}
      </div>
    </div>

    <div class="mb-3">
      <div class="info-label">Jurusan</div>
      <div class="info-value">
        {{ $item->pengguna->profile->jurusan ?? '-' }}
      </div>
    </div>

    @if($item->pengguna->profile->no_hp)
    <div>
      <div class="info-label">Nomor HP</div>
      <div class="info-value">
        {{ $item->pengguna->profile->no_hp }}
      </div>
    </div>
    @endif
  </div>

  {{-- Pembimbing Info --}}
  <div class="card-footer bg-white border-top p-3">
    <h6 class="text-uppercase fw-bold text-muted mb-3" style="font-size: 0.75rem;">
      <i class="fas fa-user-tie me-2"></i>Pembimbing Lapangan
    </h6>

    @if($item->pembimbing)
    <div class="d-flex align-items-center">
      <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
        <i class="fas fa-user-tie text-primary"></i>
      </div>
      <div>
        <div class="fw-bold">{{ $item->pembimbing->nama }}</div>
        <small class="text-muted">{{ $item->pembimbing->jabatan ?? 'Pembimbing' }}</small>
      </div>
    </div>
    @else
    <div class="alert alert-warning mb-0 py-2">
      <small>Belum ditentukan</small>
    </div>
    @endif
  </div>
</div>