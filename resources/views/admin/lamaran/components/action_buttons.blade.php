<div class="d-flex gap-2">



  {{-- Logika Status --}}
  @if($lamaran->status == 'pending')
  {{-- Tombol Tolak --}}
  <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#rejectModal" data-lamaran-id="{{ $lamaran->id }}">
    <i class="fas fa-times me-1"></i> Tolak
  </button>

  {{-- Tombol Terima (Looping Attributes) --}}
  <button class="btn btn-success fw-bold"
    data-bs-toggle="modal"
    data-bs-target="#editMagangModal"
    {{-- Loop array dari controller/view --}}
    @foreach($modalDataAttributes as $key=> $value)
    data-{{ $key }}="{{ $value }}"
    @endforeach
    >
    <i class="fas fa-check me-1"></i> Terima Peserta
  </button>


  @endif

</div>