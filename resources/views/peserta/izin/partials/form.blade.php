@php
$isEdit = isset($izin); // Cek apakah variabel $izin ada (Mode Edit)
$action = $isEdit ? route('izin.update', $izin->id) : route('izin.store');
$method = $isEdit ? 'PUT' : 'POST';
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
  @csrf
  @if($isEdit)
  @method('PUT')
  @endif

  {{-- Jenis Izin --}}
  <div class="mb-3">
    <label class="form-label fw-bold">Jenis Izin</label>
    <div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="jenis_izin" id="sakit" value="sakit"
          {{ (old('jenis_izin', $izin->jenis_izin ?? '') == 'sakit') ? 'checked' : '' }} required>
        <label class="form-check-label" for="sakit">Sakit</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="jenis_izin" id="izin_lain" value="izin"
          {{ (old('jenis_izin', $izin->jenis_izin ?? '') == 'izin') ? 'checked' : '' }}>
        <label class="form-check-label" for="izin_lain">Kepentingan Lain</label>
      </div>
    </div>
  </div>

  {{-- TANGGAL IZIN (MULTIPLE SELECT) --}}
  <div class="mb-3">
    <label class="form-label fw-bold">Pilih Tanggal Izin</label>
    <input type="text" name="tanggal_izin" id="tanggal_picker" class="form-control bg-white"
      placeholder="Klik untuk memilih tanggal..." readonly required>
    <div class="form-text text-muted">
      <i class="fas fa-info-circle me-1"></i>
      Pilih tanggal yang ingin diajukan/diubah.
    </div>
  </div>

  {{-- Alasan --}}
  <div class="mb-3">
    <label class="form-label fw-bold">Alasan</label>
    <textarea name="alasan" class="form-control" rows="3" required>{{ old('alasan', $izin->alasan ?? '') }}</textarea>
  </div>

  {{-- Upload --}}
  <div class="mb-4">
    <label class="form-label fw-bold">Bukti (Opsional)</label>
    @if($isEdit && $izin->bukti_lampiran)
    <div class="mb-2">
      <a href="{{ asset('storage/'.$izin->bukti_lampiran) }}" target="_blank" class="badge bg-info text-decoration-none">Lihat Bukti Saat Ini</a>
    </div>
    @endif
    <input type="file" name="bukti_lampiran" class="form-control" accept="image/*,.pdf">
    @if($isEdit)
    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah bukti.</small>
    @endif
  </div>

  <div class="d-grid gap-2">
    <button type="submit" class="btn btn-primary fw-bold">
      {{ $isEdit ? 'Simpan Perubahan' : 'Kirim Pengajuan' }}
    </button>
    <a href="{{ route('izin.index') }}" class="btn btn-light">Batal</a>
  </div>
</form>