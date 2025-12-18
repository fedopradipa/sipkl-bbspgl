{{--
    Partial: Modal Konfirmasi Lulus
    Menerima parameter: $item (Object Lamaran), $isOverdue (Boolean)
--}}
<div class="modal fade text-start" id="lulusModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h6 class="modal-title fw-bold"><i class="fas fa-user-graduate me-2"></i>Konfirmasi Kelulusan</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.lamaran.updateStatus', $item->id) }}" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="selesai">

                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <div class="avatar-lg bg-light-success rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-check-circle fa-3x text-success"></i>
                        </div>
                        <h5 class="fw-bold">{{ $item->pengguna->name }}</h5>
                        <p class="text-muted small">Akan dinyatakan LULUS magang.</p>
                    </div>

                    @if($isOverdue)
                    <div class="alert alert-warning d-flex align-items-center small border-0 shadow-sm">
                        <i class="fas fa-clock fa-lg me-3"></i>
                        <div>Masa magang peserta ini sudah berakhir.</div>
                    </div>
                    @endif

                    <div class="alert alert-info small d-flex align-items-start border-0 bg-light-info text-dark">
                        <i class="fas fa-info-circle mt-1 me-2 text-primary"></i>
                        <div>
                            Pastikan peserta sudah:
                            <ul class="mb-0 ps-3 mt-1">
                                <li>Mengumpulkan Laporan Akhir</li>
                                <li>Mengembalikan Aset Kantor</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold">Ya, Luluskan</button>
                </div>
            </form>
        </div>
    </div>
</div>