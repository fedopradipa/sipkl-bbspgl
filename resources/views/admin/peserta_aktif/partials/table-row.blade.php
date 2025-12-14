@php
// Cek apakah tanggal selesai sudah lewat hari ini
$isOverdue = \Carbon\Carbon::now()->gt($item->tgl_selesai_resmi);
$rowClass = $isOverdue ? 'table-warning' : '';

// Setup Avatar
$filename = $item->pengguna->profile->foto_profil ?? null;
$fotoUrl = $filename ? asset('storage/foto_profil/' . $filename) : 'https://ui-avatars.com/api/?name='.urlencode($item->pengguna->name).'&background=random&color=fff';
@endphp

<tr class="{{ $rowClass }}">
  {{-- KOLOM 1: PROFIL --}}
  <td class="ps-4">
    <div class="d-flex align-items-center">
      <img src="{{ $fotoUrl }}" class="rounded-circle me-3 border" width="40" height="40" style="object-fit: cover;">
      <div>
        <div class="fw-bold text-dark">{{ $item->pengguna->name }}</div>
        @if($isOverdue)
        <span class="badge bg-danger blink-animation small mt-1">
          <i class="fas fa-exclamation-circle"></i> Masa Magang Habis
        </span>
        @else
        <div class="small text-muted">
          <i class="fas fa-id-card me-1"></i> {{ $item->pengguna->profile->nim_nis ?? '-' }}
        </div>
        @endif
      </div>
    </div>
  </td>

  {{-- KOLOM 2: POSISI --}}
  <td>
    <div class="small fw-bold">{{ $item->lowongan->nama_posisi }}</div>
    <div class="small text-muted">
      {{ $item->tgl_selesai_resmi->format('d M Y') }}
      @if($isOverdue) (Lewat) @endif
    </div>
  </td>

  {{-- KOLOM 3: PROGRESS BAR --}}
  <td>
    @include('admin.peserta_aktif.components.progress-bar', [
    'start' => $item->tgl_mulai_resmi,
    'end' => $item->tgl_selesai_resmi
    ])
  </td>

  {{-- KOLOM 4: PEMBIMBING --}}
  <td>
    @if($item->pembimbing)
    <div class="small"><i class="fas fa-user-tie text-muted me-1"></i> {{ $item->pembimbing->nama }}</div>
    @else
    <span class="badge bg-danger">Belum ada</span>
    @endif
  </td>

  {{-- KOLOM 5: AKSI --}}
  <td class="text-end pe-4">

    {{-- UPDATE: Tampilkan Group Tombol SELALU (Baik overdue maupun tidak) --}}
    <div class="btn-group">

      {{-- 1. DETAIL --}}
      <a href="{{ route('admin.peserta_aktif.show', $item->id) }}" class="btn btn-sm btn-light text-primary" title="Detail">
        <i class="fas fa-eye"></i>
      </a>

      {{-- 2. EDIT (PERPANJANG MASA MAGANG) --}}
      {{-- Tombol ini sekarang SELALU MUNCUL, jadi Admin bisa revisi tanggal selesai --}}
      <button type="button" class="btn btn-sm btn-light text-warning"
        data-bs-toggle="modal" data-bs-target="#editMagangModal"
        data-lamaran-id="{{ $item->id }}"
        data-pembimbing-id="{{ $item->pembimbing_id }}"
        data-start="{{ $item->tgl_mulai_resmi ? $item->tgl_mulai_resmi->format('Y-m-d') : '' }}"
        data-end="{{ $item->tgl_selesai_resmi ? $item->tgl_selesai_resmi->format('Y-m-d') : '' }}"
        data-days="{{ json_encode($item->hari_kerja_resmi ?? []) }}"
        title="Edit / Perpanjang">
        <i class="fas fa-pen"></i>
      </button>

      {{-- 3. LULUSKAN --}}
      {{-- Kita beri efek visual 'Blink' atau warna beda JIKA Overdue agar Admin sadar --}}
      <button type="button" class="btn btn-sm {{ $isOverdue ? 'btn-success blink-animation' : 'btn-light text-success' }}"
        data-bs-toggle="modal" data-bs-target="#lulusModal{{ $item->id }}"
        title="Verifikasi & Luluskan">
        <i class="fas fa-graduation-cap"></i>
      </button>

      {{-- 4. DROP OUT --}}
      <button type="button" class="btn btn-sm btn-light text-danger"
        data-bs-toggle="modal" data-bs-target="#dropoutModal{{ $item->id }}" title="Hentikan">
        <i class="fas fa-ban"></i>
      </button>
    </div>

    {{-- MODAL LULUS (Tetap Sama) --}}
    <div class="modal fade text-start" id="lulusModal{{ $item->id }}" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title">Konfirmasi Kelulusan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="{{ route('admin.lamaran.updateStatus', $item->id) }}" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="status" value="selesai">
            <div class="modal-body">
              <p>Apakah Anda yakin meluluskan <strong>{{ $item->pengguna->name }}</strong>?</p>

              @if($isOverdue)
              <div class="alert alert-warning small">
                <i class="fas fa-clock me-1"></i> Masa magang peserta ini sudah lewat.
              </div>
              @endif

              <small class="text-muted">Pastikan laporan akhir sudah diterima & aset dikembalikan.</small>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-success">Ya, Luluskan</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- MODAL DROP OUT (Tetap Sama) --}}
    <div class="modal fade text-start" id="dropoutModal{{ $item->id }}" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">Hentikan Peserta</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="{{ route('admin.lamaran.updateStatus', $item->id) }}" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="status" value="berhenti">

            <div class="modal-body">
              {{-- Opsi Kategori --}}
              <p class="fw-bold mb-2">Pilih Kategori:</p>
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="kategori" id="cat1-{{$item->id}}"
                  value="Mengundurkan Diri" checked>
                <label class="form-check-label" for="cat1-{{$item->id}}">Mengundurkan Diri (Sakit/Pribadi)</label>
              </div>
              <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="kategori" id="cat2-{{$item->id}}"
                  value="Diberhentikan">
                <label class="form-check-label" for="cat2-{{$item->id}}">Diberhentikan (Pelanggaran)</label>
              </div>

              {{-- Detail Textarea --}}
              <div class="mb-3">
                <label for="note-{{$item->id}}" class="form-label fw-bold">Detail Catatan Admin <span class="text-danger">*</span></label>
                <textarea name="catatan_detail" id="note-{{$item->id}}" class="form-control" rows="3"
                  placeholder="Contoh: Sakit Typus..." required></textarea>
              </div>

              <div class="alert alert-warning small mb-0">
                <i class="fas fa-exclamation-triangle me-1"></i> Akses absensi akan ditutup.
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-danger">Simpan Status Berhenti</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </td>
</tr>