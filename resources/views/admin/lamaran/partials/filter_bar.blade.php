<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center w-100 gap-3">

  {{-- BAGIAN KIRI: Filter Tab Status (Pills Style) --}}
  <div class="btn-group shadow-sm">
    @foreach(['pending', 'ditolak'] as $st)
    <a href="{{ route('admin.lamaran.index', array_merge(request()->query(), ['status' => $st])) }}"
      class="btn btn-sm px-3 {{ $currentStatus == $st ? 'btn-primary' : 'btn-white border bg-white text-secondary' }}">

      {{-- Ikon --}}
      @if($st == 'pending') <i class="fas fa-inbox me-1"></i>
      @elseif($st == 'ditolak') <i class="fas fa-history me-1"></i>
      @endif

      {{ ucfirst($st) }}
    </a>
    @endforeach
  </div>

  {{-- BAGIAN KANAN: Lowongan & Search --}}
  <form action="{{ route('admin.lamaran.index') }}" method="GET" class="d-flex flex-column flex-md-row gap-2">
    {{-- Pertahankan status saat filter lowongan/keyword berubah --}}
    <input type="hidden" name="status" value="{{ $currentStatus }}">

    {{-- Filter Lowongan --}}
    <select name="lowongan_id"
      class="form-select form-select-sm shadow-sm bg-white"
      style="min-width: 200px; cursor: pointer;"
      onchange="this.form.submit()">
      <option value="">-- Semua Posisi --</option>
      @foreach($lowongans as $l)
      <option value="{{ $l->id }}" {{ request('lowongan_id') == $l->id ? 'selected' : '' }}>
        {{ $l->posisi }}
      </option>
      @endforeach
    </select>

    {{-- Pencarian --}}
    <div class="input-group input-group-sm shadow-sm" style="min-width: 250px;">
      <input type="text"
        name="keyword"
        class="form-control"
        placeholder="Cari nama pelamar..."
        value="{{ request('keyword') }}">

      <button class="btn btn-primary" type="submit">
        <i class="fas fa-search"></i>
      </button>

      @if(request('keyword') || request('lowongan_id'))
      <a href="{{ route('admin.lamaran.index', ['status' => $currentStatus]) }}"
        class="btn btn-outline-secondary"
        title="Reset Filter">
        <i class="fas fa-times"></i>
      </a>
      @endif
    </div>
  </form>
</div>