{{-- MODAL SHARED: EDIT / APPROVE MAGANG --}}
<div class="modal fade" id="editMagangModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      {{-- Header akan berubah warna via JS --}}
      <div class="modal-header bg-warning text-dark bg-opacity-25">
        <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Edit Data Magang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="editMagangForm" method="POST" action="">
        @csrf
        {{-- Method POST digunakan untuk route approve/update --}}

        <div class="modal-body">
          <p class="text-muted small mb-3" id="modalDescription">
            Silakan lengkapi data resmi magang di bawah ini.
          </p>

          {{-- 1. Pilih Pembimbing --}}
          <div class="mb-3">
            <label for="pembimbing_id" class="form-label small fw-bold text-muted">Pembimbing Lapangan <span class="text-danger">*</span></label>
            <select name="pembimbing_id" id="pembimbing_id" class="form-select" required>
              <option value="">-- Pilih Pembimbing --</option>
              @foreach($mentors as $mentor)
              <option value="{{ $mentor->id }}">{{ $mentor->nama }}</option>
              @endforeach
            </select>
          </div>

          {{-- 2. Tanggal Resmi --}}
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="tgl_mulai_resmi" class="form-label small fw-bold text-muted">Mulai Resmi <span class="text-danger">*</span></label>
              <input type="date" name="tgl_mulai_resmi" id="tgl_mulai_resmi" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="tgl_selesai_resmi" class="form-label small fw-bold text-muted">Selesai Resmi <span class="text-danger">*</span></label>
              <input type="date" name="tgl_selesai_resmi" id="tgl_selesai_resmi" class="form-control" required>
            </div>
          </div>

          {{-- 3. Hari Kerja Resmi --}}
          <div class="mb-3 border p-3 rounded bg-light">
            <label class="form-label fw-bold d-block small text-muted mb-2">Hari Kerja Wajib <span class="text-danger">*</span></label>
            @foreach(['Monday'=>'Senin', 'Tuesday'=>'Selasa', 'Wednesday'=>'Rabu', 'Thursday'=>'Kamis', 'Friday'=>'Jumat'] as $dayEng => $dayInd)
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="hari_kerja_resmi[]" id="day-{{ $dayEng }}" value="{{ $dayInd }}">
              <label class="form-check-label small" for="day-{{ $dayEng }}">{{ $dayInd }}</label>
            </div>
            @endforeach
          </div>
        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-white border" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold px-4">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- SCRIPT OTOMATIS --}}
{{-- Script ini ditaruh di sini agar langsung jalan saat file di-include --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editMagangModal');

    // Pastikan elemen ada sebelum pasang event listener
    if (editModal) {
      editModal.addEventListener('show.bs.modal', function(event) {
        // 1. Identifikasi Tombol Pemicu
        const button = event.relatedTarget;

        // 2. Ambil Data dari Atribut Tombol
        const mode = button.getAttribute('data-mode'); // 'approve' atau null (edit)
        const lamaranId = button.getAttribute('data-lamaran-id');
        const pembimbingId = button.getAttribute('data-pembimbing-id');
        const tglStart = button.getAttribute('data-start');
        const tglEnd = button.getAttribute('data-end');
        const daysJson = button.getAttribute('data-days');

        // 3. UI MANIPULATION (Hijau vs Kuning)
        const modalHeader = editModal.querySelector('.modal-header');
        const modalTitle = editModal.querySelector('.modal-title');
        const modalDesc = document.getElementById('modalDescription');
        const submitBtn = editModal.querySelector('button[type="submit"]');

        // Reset Class Header
        modalHeader.classList.remove('bg-success', 'bg-warning', 'text-dark', 'text-dark');
        submitBtn.classList.remove('btn-success', 'btn-primary');

        if (mode === 'approve') {
          // MODE TERIMA (Hijau)
          modalHeader.classList.add('bg-success', 'text-dark', 'bg-opacity-25');
          modalTitle.innerHTML = '<i class="fas fa-check-circle me-2"></i>Finalisasi Penerimaan';
          modalDesc.textContent = "Konfirmasi penerimaan peserta dan tetapkan jadwal resmi.";
          submitBtn.innerText = 'Terima Peserta';
          submitBtn.classList.add('btn-success');
        } else {
          // MODE EDIT (Kuning/Warning)
          modalHeader.classList.add('bg-warning', 'text-dark', 'bg-opacity-25');
          modalTitle.innerHTML = '<i class="fas fa-edit me-2"></i>Edit Data Magang';
          modalDesc.textContent = "Ubah data resmi pembimbing atau jadwal magang.";
          submitBtn.innerText = 'Simpan Perubahan';
          submitBtn.classList.add('btn-primary');
        }

        // 4. SETUP ACTION FORM
        // Pastikan route ini sesuai dengan web.php Anda (/admin/lamaran/{id}/approve)
        const form = document.getElementById('editMagangForm');
        form.action = '/admin/lamaran/' + lamaranId + '/approve';

        // 5. ISI INPUT FIELD (Populate)

        // Pembimbing
        const selectPembimbing = document.getElementById('pembimbing_id');
        if (selectPembimbing) selectPembimbing.value = pembimbingId || "";

        // Tanggal
        document.getElementById('tgl_mulai_resmi').value = tglStart || "";
        document.getElementById('tgl_selesai_resmi').value = tglEnd || "";

        // 6. ISI CHECKBOX (Parsing JSON)
        // Reset semua checkbox
        document.querySelectorAll('#editMagangModal input[name="hari_kerja_resmi[]"]').forEach(box => box.checked = false);

        if (daysJson) {
          try {
            // Handle jika JSON tersimpan sebagai string berulang
            const daysArray = typeof daysJson === 'string' ? JSON.parse(daysJson) : daysJson;

            if (Array.isArray(daysArray)) {
              daysArray.forEach(day => {
                // Cari checkbox dengan value yang cocok (Senin, Selasa, dst)
                const box = document.querySelector(`#editMagangModal input[value="${day}"]`);
                if (box) box.checked = true;
              });
            }
          } catch (e) {
            console.error("Gagal memproses data hari kerja:", e);
          }
        }
      });
    }
  });
</script>