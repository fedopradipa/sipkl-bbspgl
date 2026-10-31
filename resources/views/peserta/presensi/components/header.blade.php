<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h5 class="text-muted mb-0 small">Halo!</h5>
    <h2 class="fw-bold text-dark">{{ $user->name ?? $user->nama }}</h2>
  </div>
  <div class="bg-white p-1 rounded-circle shadow-sm border">
    @if($user->foto_profil)
    <img
      src="{{ asset('storage/foto_profil/' . $user->foto_profil) }}"
      class="rounded-circle"
      width="50"
      height="50"
      style="object-fit: cover;"
      alt="{{ $user->nama }}">
    @else
    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
      <i class="fas fa-user text-secondary"></i>
    </div>
    @endif
  </div>
</div>