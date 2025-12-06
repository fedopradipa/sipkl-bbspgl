<div class="modal fade" id="createModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="{{ route('presensi.store.admin') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Input Absen Manual</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info small">
          <i class="fas fa-info-circle me-1"></i>
          Digunakan jika peserta lupa absen atau izin sakit.
        </div>

        <div class="mb-3">
          <label class="form-label">Pilih Peserta <span class="text-danger">*</span></label>
          <select name="lamaran_id" class="form-select" required>
            <option value="">-- Pilih Peserta Aktif --</option>
            @foreach($pesertaAktif as $p)
            <option value="{{ $p->id }}">{{ $p->pengguna->name }} ({{ $p->lowongan->divisi }})</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Tanggal <span class="text-danger">*</span></label>
          <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>

        {{-- PILIHAN STATUS --}}
        {{-- Tambahkan ID "statusSelect" untuk dideteksi JS --}}
        <div class="mb-3">
          <label class="form-label">Status <span class="text-danger">*</span></label>
          <select name="status" id="statusSelect" class="form-select" required>
            <option value="hadir" selected>Hadir</option>
            <option value="sakit">Sakit</option>
            <option value="izin">Izin</option>
            <option value="alpha">Alpha</option>
          </select>
        </div>

        {{-- INPUT JAM (Akan disembunyikan jika Sakit/Izin) --}}
        {{-- Bungkus dalam ID "jamContainer" --}}
        <div class="row" id="jamContainer">
          <div class="col-6 mb-3">
            <label class="form-label">Jam Masuk <span class="text-danger">*</span></label>
            <input type="time" name="jam_masuk" id="jamMasukInput" class="form-control" value="08:00" required>
          </div>
          <div class="col-6 mb-3">
            <label class="form-label">Jam Pulang</label>
            <input type="time" name="jam_pulang" id="jamPulangInput" class="form-control">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Simpan Data</button>
      </div>
    </form>
  </div>
</div>

{{-- SCRIPT INTERAKTIF --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('statusSelect');
    const jamContainer = document.getElementById('jamContainer');
    const jamMasukInput = document.getElementById('jamMasukInput');
    const jamPulangInput = document.getElementById('jamPulangInput');

    function toggleJamInputs() {
      if (statusSelect.value === 'hadir') {
        // Jika HADIR: Tampilkan
        jamContainer.style.display = 'flex';
        jamMasukInput.required = true;
        // Set default jam jika kosong
        if (!jamMasukInput.value) jamMasukInput.value = "08:00";
      } else {
        // Jika SAKIT/IZIN: Sembunyikan & Kosongkan Nilainya
        jamContainer.style.display = 'none';
        jamMasukInput.required = false;

        // PENTING: Kosongkan value agar tidak terkirim ke controller
        jamMasukInput.value = '';
        jamPulangInput.value = '';
      }
    }

    statusSelect.addEventListener('change', toggleJamInputs);
    toggleJamInputs();
  });
</script>