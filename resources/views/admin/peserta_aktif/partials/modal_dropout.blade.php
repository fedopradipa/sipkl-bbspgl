{{--
    Partial: Modal Konfirmasi Drop Out / Berhenti
    Menerima parameter: $item (Object Lamaran)
--}}
<div class="modal fade text-start" id="dropoutModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-danger text-white">
        <h6 class="modal-title fw-bold"><i class="fas fa-user-times me-2"></i>Hentikan Peserta</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form action="{{ route('admin.lamaran.updateStatus', $item->id) }}" method="POST">
        @csrf @method('PUT')
        <input type="hidden" name="status" value="berhenti">

        <div class="modal-body p-4">
          <p class="mb-3">Pilih kategori pemberhentian untuk <strong>{{ $item->pengguna->name }}</strong>:</p>

          <div class="card bg-light border-0 mb-3">
            <div class="card-body">
              {{-- Opsi 1: Mengundurkan Diri --}}
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="kategori" id="cat1-{{$item->id}}" value="Mengundurkan Diri" checked>
                <label class="form-check-label fw-bold text-dark" for="cat1-{{$item->id}}">
                  Mengundurkan Diri
                </label>
                <div class="small text-muted ps-0">Karena alasan sakit, pribadi, atau akademik.</div>
              </div>
              <hr class="my-2">
              {{-- Opsi 2: Diberhentikan --}}
              <div class="form-check">
                <input class="form-check-input" type="radio" name="kategori" id="cat2-{{$item->id}}" value="Diberhentikan">
                <label class="form-check-label fw-bold text-danger" for="cat2-{{$item->id}}">
                  Diberhentikan (DO)
                </label>
                <div class="small text-muted ps-0">Karena pelanggaran tata tertib atau disiplin.</div>
              </div>
            </div>
          </div>

          <div class="mb-2">
            <label for="note-{{$item->id}}" class="form-label small text-uppercase text-muted fw-bold">Detail Alasan <span class="text-danger">*</span></label>
            <textarea name="catatan_detail" id="note-{{$item->id}}" class="form-control" rows="3" placeholder="Tuliskan alasan detail..." required></textarea>
          </div>
        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger fw-bold">Simpan Status Berhenti</button>
        </div>
      </form>
    </div>
  </div>
</div>