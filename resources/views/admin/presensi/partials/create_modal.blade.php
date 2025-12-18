<div class="modal fade" id="createModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="{{ route('admin.presensi.store') }}" method="POST" class="modal-content">
      @csrf

      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalTitle">Input Absen Manual</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        {{-- INFO BANNER --}}
        <div class="alert alert-light border-start border-4 border-success small mb-3">
          <i class="fas fa-info-circle text-success me-1"></i>
          <span id="infoText">Digunakan jika peserta lupa absen atau berhalangan.</span>
        </div>

        {{-- 1. INPUT PESERTA (Auto-Lock jika dari tabel) --}}
        <div class="mb-3">
          <label class="form-label fw-bold small text-uppercase text-muted">Peserta</label>
          <select name="lamaran_id" id="create_lamaran_id" class="form-select" required>
            <option value="">-- Pilih Peserta --</option>
            @foreach($pesertaAktif as $p)
            <option value="{{ $p->id }}">{{ $p->pengguna->name }} ({{ $p->lowongan->divisi }})</option>
            @endforeach
          </select>
          {{-- Hidden input untuk memastikan value terkirim saat select didisable secara visual --}}
        </div>

        {{-- 2. INPUT TANGGAL (Auto-Lock jika dari tabel) --}}
        <div class="mb-3">
          <label class="form-label fw-bold small text-uppercase text-muted">Tanggal</label>
          <input type="date" name="tanggal" id="create_tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>

        <hr class="border-secondary opacity-10 my-4">

        {{-- 3. PILIHAN STATUS --}}
        <div class="mb-3">
          <label class="form-label fw-bold small text-uppercase text-muted">Status Kehadiran</label>
          <select name="status" id="create_status" class="form-select bg-light border-primary" required>
            <option value="hadir" selected>Hadir (Masuk Kerja)</option>
            <option value="sakit">Sakit</option>
            <option value="izin">Izin</option>
            <option value="alpha">Alpha (Tanpa Keterangan)</option>
          </select>
        </div>

        {{-- 4. KONTAINER JAM (Hanya Muncul jika HADIR) --}}
        <div class="row g-2" id="jamContainer">
          <div class="col-6">
            <label class="form-label small">Jam Masuk <span class="text-danger">*</span></label>
            <input type="time" name="jam_masuk" id="create_masuk" class="form-control" value="08:00">
          </div>
          <div class="col-6">
            <label class="form-label small">Jam Pulang</label>
            <input type="time" name="jam_pulang" id="create_pulang" class="form-control">
          </div>
        </div>

        {{-- 5. KONTAINER CATATAN (Muncul jika SAKIT/IZIN/ALPHA) --}}
        <div class="mb-3" id="noteContainer" style="display: none;">
          <label class="form-label small">Catatan / Keterangan (Opsional)</label>
          <textarea name="catatan_harian" id="create_catatan" class="form-control" rows="2"
            placeholder="Contoh: Sakit demam, ada surat dokter..."></textarea>
          <div class="form-text small text-muted">
            <i class="fas fa-magic me-1"></i> Jika dikosongkan, sistem akan mengisi otomatis: "Diinput manual oleh Admin (Sakit/Izin)".
          </div>
        </div>

      </div>

      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success px-4 fw-bold">Simpan Data</button>
      </div>
    </form>
  </div>
</div>

{{-- JAVASCRIPT LOGIC --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const createModal = document.getElementById('createModal');

    if (createModal) {
      // Definisi Elemen
      const elPeserta = document.getElementById('create_lamaran_id');
      const elTanggal = document.getElementById('create_tanggal');
      const elStatus = document.getElementById('create_status');
      const elJamContainer = document.getElementById('jamContainer');
      const elNoteContainer = document.getElementById('noteContainer');
      const elJamMasuk = document.getElementById('create_masuk');
      const elModalTitle = document.getElementById('modalTitle');

      // 1. Logic Ganti Tampilan Berdasarkan Status
      function toggleStatusUI() {
        const status = elStatus.value;

        if (status === 'hadir') {
          // Tampilkan Jam, Sembunyikan Catatan
          elJamContainer.style.display = 'flex';
          elNoteContainer.style.display = 'none';
          elJamMasuk.required = true;
        } else {
          // Sembunyikan Jam, Tampilkan Catatan
          elJamContainer.style.display = 'none';
          elNoteContainer.style.display = 'block';
          elJamMasuk.required = false;

          // Reset Jam agar bersih saat disubmit
          elJamMasuk.value = '';
          document.getElementById('create_pulang').value = '';

          // Otomatis isi catatan jika Alpha 
          if (status === 'alpha') {
            document.getElementById('create_catatan').placeholder = "Tanpa keterangan...";
          }
        }
      }

      // 2. Event Listener saat Modal Dibuka
      createModal.addEventListener('show.bs.modal', function(event) {
        const btn = event.relatedTarget;

        // Ambil data dari tombol (jika ada)
        const dataId = btn.getAttribute('data-lamaran-id');
        const dataTanggal = "{{ $tanggal ?? date('Y-m-d') }}"; // Ambil dari Controller variable $tanggal

        if (dataId) {
          // === MODE: INPUT DARI TABEL (Contextual Input) ===
          elModalTitle.innerText = "Input Kehadiran Peserta";

          // Isi & Kunci Peserta
          elPeserta.value = dataId;
          elPeserta.style.pointerEvents = 'none'; // User tidak bisa klik
          elPeserta.style.backgroundColor = '#e9ecef'; // Visual abu-abu (Readonly look)

          // Isi & Kunci Tanggal
          elTanggal.value = dataTanggal;
          elTanggal.readOnly = true;
        } else {
          // === MODE: INPUT MANUAL UMUM ===
          elModalTitle.innerText = "Input Absen Manual";

          // Reset & Buka Kunci
          elPeserta.value = "";
          elPeserta.style.pointerEvents = 'auto';
          elPeserta.style.backgroundColor = '#fff';

          elTanggal.value = "{{ date('Y-m-d') }}";
          elTanggal.readOnly = false;
        }

        // Reset Status ke Default (Hadir)
        elStatus.value = 'hadir';
        toggleStatusUI();
      });

      // 3. Event Listener saat Status Berubah
      elStatus.addEventListener('change', toggleStatusUI);
    }
  });
</script>