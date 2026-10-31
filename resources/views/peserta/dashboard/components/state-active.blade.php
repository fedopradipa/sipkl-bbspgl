{{-- State: Active (Masa PKL) --}}
{{-- Versi Ringkas untuk Dashboard --}}

<div class="row">
  <div class="col-12">

    {{-- Card Status Aktif --}}
    <div class="card border-0 shadow-sm bg-primary text-white overflow-hidden mb-4 position-relative">
      {{-- Hiasan Background --}}
      <div class="position-absolute end-0 top-0 opacity-25 me-n3 mt-n3">
        <i class="fas fa-briefcase fa-10x"></i>
      </div>

      <div class="card-body p-4 position-relative z-1">
        <div class="row align-items-start">

          {{-- ========================================== --}}
          {{-- KOLOM KIRI: Status, Hari Kerja, Tombol --}}
          {{-- ========================================== --}}
          <div class="col-md-7 col-lg-8 mb-4 mb-md-0">
            <h4 class="fw-bold mb-1">Status: Sedang PKL</h4>
            <p class="mb-3 opacity-75">
              Anda terdaftar sebagai peserta aktif pada posisi:
              <br>
              <strong class="fs-5">{{ $lamaran->lowongan->posisi ?? 'Lowongan' }}</strong>
            </p>

            {{-- Tampilan Hari Kerja --}}
            <div class="mb-4">
              <small class="d-block text-uppercase opacity-75 fw-bold mb-1" style="font-size: 0.7rem;">
                <i class="fas fa-calendar-day me-1"></i> Jadwal Kerja
              </small>
              <div>
                @if(isset($lamaran->hari_kerja_resmi) && is_array($lamaran->hari_kerja_resmi))
                @foreach($lamaran->hari_kerja_resmi as $hari)
                <span class="badge bg-white bg-opacity-25 border border-white border-opacity-25 me-1 mb-1">
                  {{ $hari }}
                </span>
                @endforeach
                @else
                <span class="text-white opacity-50">-</span>
                @endif
              </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
              {{-- TOMBOL UTAMA KE HALAMAN PRESENSI --}}
              <a href="{{ route('presensi.index') }}"
                class="btn btn-light text-primary fw-bold shadow-sm rounded-pill px-4">
                <i class="fas fa-clock me-2"></i> Buka Presensi
              </a>

              <a href="{{ route('izin.index') }}"
                class="btn btn-outline-light fw-bold rounded-pill px-4">
                Izin
              </a>
            </div>
          </div>

          {{-- ========================================== --}}
          {{-- KOLOM KANAN: Detail Penempatan --}}
          {{-- ========================================== --}}
          <div class="col-md-5 col-lg-4">

            {{-- 1. Info posisi --}}
            <div class="bg-white bg-opacity-10 p-3 rounded border border-white border-opacity-25 mb-2">
              <small class="d-block text-uppercase opacity-75" style="font-size: 0.7rem;">
                <i class="fas fa-building me-1"></i> posisi / Departemen
              </small>
              {{-- Mengambil field posisi, fallback ke posisi lowongan jika kosong --}}
              <span class="fw-bold d-block">
                {{ $lamaran->lowongan->posisi ?? $lamaran->lowongan->posisi }}
              </span>
            </div>

            {{-- 2. Info Pembimbing & Kontak --}}
            <div class="bg-white bg-opacity-10 p-3 rounded border border-white border-opacity-25 mb-2">
              <small class="d-block text-uppercase opacity-75" style="font-size: 0.7rem;">
                <i class="fas fa-user-tie me-1"></i> Pembimbing Lapangan
              </small>
              <span class="fw-bold text-truncate d-block mb-1">{{ $lamaran->pembimbing->nama ?? '-' }}</span>

              {{-- Tombol WhatsApp Pembimbing --}}
              @if(isset($lamaran->pembimbing->no_hp))
              @php
              // Format nomor HP ke format WA (62...)
              $hp = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $lamaran->pembimbing->no_hp));
              @endphp
              <a href="https://wa.me/{{ $hp }}" target="_blank" class="text-white text-decoration-none small opacity-75 hover-scale d-inline-block">
                <i class="fab fa-whatsapp me-1"></i> {{ $lamaran->pembimbing->no_hp }}
              </a>
              @else
              <small class="text-white opacity-50">- No HP Tidak Tersedia -</small>
              @endif
            </div>

            {{-- 3. Info Periode --}}
            <div class="bg-white bg-opacity-10 p-3 rounded border border-white border-opacity-25">
              <small class="d-block text-uppercase opacity-75" style="font-size: 0.7rem;">
                <i class="fas fa-hourglass-half me-1"></i> Periode PKL
              </small>
              <div class="fw-bold small">
                {{ \Carbon\Carbon::parse($lamaran->tgl_mulai_resmi)->translatedFormat('d M Y') }}
                <span class="opacity-50 mx-1">-</span>
                {{ \Carbon\Carbon::parse($lamaran->tgl_selesai_resmi)->translatedFormat('d M Y') }}
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- Menu Cepat --}}