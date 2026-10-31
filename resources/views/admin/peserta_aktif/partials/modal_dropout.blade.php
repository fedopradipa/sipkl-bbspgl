{{--
    Partial: Modal Konfirmasi Berhenti
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
        @csrf
        @method('PUT')

        {{-- status utama --}}
        <input type="hidden" name="status" value="berhenti">

        {{-- kategori DO dihapus, hanya 1 kategori --}}
        <input type="hidden" name="kategori" value="Mengundurkan Diri">

        <div class="modal-body p-4">
          <p class="mb-3">
            Anda akan menghentikan peserta <strong>{{ $item->pengguna->name }}</strong>.
          </p>

          <div class="alert alert-warning small mb-3">
            Status akan diubah menjadi <strong>Berhenti</strong>.
          </div>

          <div class="mb-2">
            <label for="note-{{$item->id}}" class="form-label small text-uppercase text-muted fw-bold">
              Detail Alasan <span class="text-danger">*</span>
            </label>
            <textarea
              name="catatan_detail"
              id="note-{{$item->id}}"
              class="form-control"
              rows="3"
              placeholder="Tuliskan alasan detail..."
              required></textarea>
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