<div class="row justify-content-center">
  <div class="col-lg-10">

    {{-- Alert Info --}}
    <div class="alert alert-warning shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
      <div class="bg-warning text-white rounded p-3 me-3">
        <i class="fas fa-clock fa-2x"></i>
      </div>
      <div>
        <h5 class="alert-heading fw-bold mb-1">Masa PKL Telah Berakhir</h5>
        <p class="mb-0 small">
          Periode PKL Anda berakhir pada <strong>{{ \Carbon\Carbon::parse($lamaran->tgl_selesai_resmi)->translatedFormat('d F Y') }}</strong>.
          Silakan tunggu untuk diproses kelulusannya oleh Admin.
        </p>
      </div>
    </div>



  </div>
</div>
</div>