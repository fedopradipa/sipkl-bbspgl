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
      const lamaranId = button.getAttribute('data-lamaran-id');
      const startUsulan = button.getAttribute('data-start-usulan');
      const endUsulan = button.getAttribute('data-end-usulan'); // Ambil end date
      const daysUsulan = button.getAttribute('data-days-usulan'); // Ambil array hari kerja

      const form = document.getElementById('approveForm');

      // 1. SET FORM ACTION (URI)
      form.action = '/lamaran/' + lamaranId + '/approve';

      // 2. SET TANGGAL RESMI (Default ke Tanggal Usulan)
      document.getElementById('tgl_mulai_resmi').value = startUsulan;
      document.getElementById('tgl_selesai_resmi').value = endUsulan; // <-- Otomatis terisi

      // Tampilkan tanggal usulan di bawah input
      document.getElementById('usulanStart').textContent = new Date(startUsulan).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });

      // 3. OPSI UTAMA: CENTANG HARI KERJA OTOMATIS

      // Pertama, hapus semua centang yang mungkin tersisa dari sesi sebelumnya
      document.querySelectorAll('#approveModal input[name="hari_kerja_resmi[]"]').forEach(checkbox => {
        checkbox.checked = false;
      });

      if (daysUsulan) {
        // Karena kita kirim data-days-usulan dari PHP (yang sudah di-cast jadi array INDO), 
        // kita harus parse string JSON itu kembali ke JavaScript Array.
        const selectedDays = JSON.parse(daysUsulan);

        selectedDays.forEach(dayIndo => {
          // Cari checkbox yang value-nya (Bahasa Indonesia) sama
          // Catatan: Value checkbox di modal ini adalah Bahasa Indonesia (Senin, Selasa, dst)
          const checkbox = document.querySelector(`#approveModal input[name="hari_kerja_resmi[]"][value="${dayIndo}"]`);

          if (checkbox) {
            checkbox.checked = true; // Centang otomatis
          }
        });
      }
    });
  });
</script>