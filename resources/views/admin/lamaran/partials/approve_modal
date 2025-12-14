<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="approveModalLabel">Finalisasi Penerimaan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      {{-- Form akan diisi action-nya via JavaScript --}}
      <form id="approveForm" method="POST" action="">
        @csrf
        <div class="modal-body">
          <p>Konfirmasi penerimaan peserta dan tetapkan detail magang resmi.</p>

          {{-- 1. Pilih Pembimbing --}}
          <div class="mb-3">
            <label for="pembimbing_id" class="form-label">Pembimbing Lapangan <span class="text-danger">*</span></label>
            <select name="pembimbing_id" id="pembimbing_id" class="form-select" required>
              <option value="">Pilih Pembimbing...</option>
              @foreach($mentors as $mentor)
              <option value="{{ $mentor->id }}">{{ $mentor->nama }}</option>
              @endforeach
            </select>
          </div>

          {{-- 2. Set Tanggal Resmi --}}
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="tgl_mulai_resmi" class="form-label">Mulai Resmi <span class="text-danger">*</span></label>
              <input type="date" name="tgl_mulai_resmi" id="tgl_mulai_resmi" class="form-control" required>
              <small class="text-muted">Tanggal Usulan: <span id="usulanStart"></span></small>
            </div>
            <div class="col-md-6 mb-3">
              <label for="tgl_selesai_resmi" class="form-label">Selesai Resmi <span class="text-danger">*</span></label>
              <input type="date" name="tgl_selesai_resmi" id="tgl_selesai_resmi" class="form-control" required>
            </div>
          </div>

          {{-- 3. Finalisasi Hari Kerja Resmi (JSON Checkboxes) --}}
          <div class="mb-3 border p-3 rounded">
            <label class="form-label fw-bold d-block">Hari Kerja Resmi <span class="text-danger">*</span></label>
            <small class="text-muted mb-2 d-block">Pilih hari wajib kehadiran di kantor.</small>
            @foreach(['Monday'=>'Senin', 'Tuesday'=>'Selasa', 'Wednesday'=>'Rabu', 'Thursday'=>'Kamis', 'Friday'=>'Jumat'] as $dayEng => $dayInd)
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="hari_kerja_resmi[]" id="day-{{ $dayEng }}" value="{{ $dayInd }}">
              <label class="form-check-label" for="day-{{ $dayEng }}">{{ $dayInd }}</label>
            </div>
            @endforeach
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Terima Peserta</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- MODAL PENOLAKAN (Sederhana) --}}
@include('admin.lamaran.partials.reject_modal')

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const approveModal = document.getElementById('approveModal');

    approveModal.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;

      // Ambil Data dari Tombol
      const lamaranId = button.getAttribute('data-lamaran-id');
      const currentStatus = button.getAttribute('data-current-status');

      // Data untuk Form (Prioritas ke Data Resmi jika Mode Edit)
      const pembimbingId = button.getAttribute('data-resmi-pembimbing');
      const tglStart = button.getAttribute('data-resmi-start');
      const tglEnd = button.getAttribute('data-resmi-end');
      const daysJson = button.getAttribute('data-resmi-days');

      // Setup Form Action
      const form = document.getElementById('approveForm');
      form.action = '/lamaran/' + lamaranId + '/approve';

      // Setup Judul Modal (UX)
      const modalTitle = document.getElementById('approveModalLabel');
      const submitBtn = form.querySelector('button[type="submit"]');

      if (currentStatus === 'diterima') {
        modalTitle.innerText = 'Edit Data Magang';
        modalTitle.classList.remove('text-success'); // Hapus warna lama jika ada logic CSS
        submitBtn.innerText = 'Simpan Perubahan';
        submitBtn.classList.replace('btn-success', 'btn-warning');
      } else {
        modalTitle.innerText = 'Finalisasi Penerimaan';
        submitBtn.innerText = 'Terima Peserta';
        submitBtn.classList.replace('btn-warning', 'btn-success');
      }

      // ISI FORM INPUT
      // 1. Pembimbing (Set Selected)
      const selectPembimbing = document.getElementById('pembimbing_id');
      selectPembimbing.value = pembimbingId || ""; // Reset jika null

      // 2. Tanggal
      document.getElementById('tgl_mulai_resmi').value = tglStart;
      document.getElementById('tgl_selesai_resmi').value = tglEnd;

      // Info text kecil
      const usulanRaw = button.getAttribute('data-usulan-start');
      document.getElementById('usulanStart').textContent = usulanRaw; // Tampilkan usulan asli sebagai pengingat

      // 3. Checkbox Hari Kerja
      // Reset dulu semua centang
      document.querySelectorAll('#approveModal input[name="hari_kerja_resmi[]"]').forEach(box => box.checked = false);

      if (daysJson) {
        try {
          const daysArray = JSON.parse(daysJson);
          daysArray.forEach(day => {
            const box = document.querySelector(`#approveModal input[value="${day}"]`);
            if (box) box.checked = true;
          });
        } catch (e) {
          console.error("Error parsing JSON days", e);
        }
      }
    });
  });
</script>