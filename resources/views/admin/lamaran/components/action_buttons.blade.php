<div class="d-flex gap-2">

  {{-- Tombol Hapus --}}
  <form action="{{ route('admin.lamaran.destroy', $lamaran->id) }}" method="POST" onsubmit="return confirm('Hapus permanen data ini?');">
    @csrf @method('DELETE')
    <button type="submit" class="btn btn-danger">
      <i class="fas fa-trash me-1"></i> Hapus
    </button>
  </form>

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

  @elseif($lamaran->status == 'ditolak')
  {{-- Tombol Terima Ulang --}}
  <button class="btn btn-success fw-bold"
    data-bs-toggle="modal"
    data-bs-target="#editMagangModal"
    @foreach($modalDataAttributes as $key=> $value)
    data-{{ $key }}="{{ $value }}"
    @endforeach
    >
    <i class="fas fa-redo me-1"></i> Terima Ulang
  </button>
  @endif

</div>