<form action="{{ route('admin.presensi.index') }}" method="GET" class="row g-3 align-items-end">

  {{-- Filter Tanggal --}}
  <div class="col-md-3">
    <label class="form-label small fw-bold text-muted text-uppercase mb-1">Tanggal</label>
    <div class="input-group">
      <span class="input-group-text bg-light border-end-0"><i class="fas fa-calendar-alt text-primary"></i></span>
      <input type="date" name="tanggal" class="form-control border-start-0 ps-0"
        value="{{ $tanggal }}" onchange="this.form.submit()">
    </div>
  </div>

  {{-- Filter Posisi (Lowongan) --}}
  <div class="col-md-3">
    <label class="form-label small fw-bold text-muted text-uppercase mb-1">Posisi</label>
    <select name="lowongan_id" class="form-select" onchange="this.form.submit()">
      <option value="">Semua Posisi</option>
      @foreach($posisi as $p)
      <option value="{{ $p->id }}" {{ $selected_posisi == $p->id ? 'selected' : '' }}>
        {{ $p->posisi }}
      </option>
      @endforeach
    </select>
  </div>

  {{-- Cari Nama --}}
  <div class="col-md-6">
    <label class="form-label small fw-bold text-muted text-uppercase mb-1">Cari Nama</label>

    <div class="d-flex gap">
      <div class="input-group">
        <input type="text" name="keyword" class="form-control" placeholder="Nama peserta..." value="{{ request('keyword') }}">
        <button class="btn btn-primary" type="submit">
          <i class="fas fa-search"></i>
        </button>
      </div>

      @if(request('keyword'))
      <a href="{{ route('admin.presensi.index', request()->except(['keyword', 'page'])) }}"
        class="btn btn-outline-secondary shadow-sm"
        title="Hapus Pencarian"
        style="min-width: 40px;">
        <i class="fas fa-times"></i>
      </a>
      @endif
    </div>
  </div>

</form>