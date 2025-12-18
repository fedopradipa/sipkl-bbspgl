<form action="{{ route('admin.presensi.index') }}" method="GET" class="row g-3 align-items-end">

  {{-- Filter Tanggal (Single Date) --}}
  <div class="col-md-3">
    <label class="form-label small fw-bold text-muted text-uppercase mb-1">Tanggal</label>
    <div class="input-group">
      <span class="input-group-text bg-light border-end-0"><i class="fas fa-calendar-alt text-primary"></i></span>
      <input type="date" name="tanggal" class="form-control border-start-0 ps-0"
        value="{{ $tanggal }}" onchange="this.form.submit()">
    </div>
  </div>

  {{-- Filter Divisi --}}
  <div class="col-md-3">
    <label class="form-label small fw-bold text-muted text-uppercase mb-1">Divisi / Posisi</label>
    <select name="divisi_id" class="form-select" onchange="this.form.submit()">
      <option value="">Semua Divisi</option>
      @foreach($divisi as $div)
      <option value="{{ $div->id }}" {{ $selected_divisi == $div->id ? 'selected' : '' }}>
        {{ $div->judul }} {{ $div->divisi ? ' - '.$div->divisi : '' }}
      </option>
      @endforeach
    </select>
  </div>

  {{-- Filter Status --}}
  <div class="col-md-3">
    <label class="form-label small fw-bold text-muted text-uppercase mb-1">Status Kehadiran</label>
    <select name="status" class="form-select" onchange="this.form.submit()">
      <option value="">Semua Status</option>
      <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
      <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
      <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
      <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
      <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
    </select>
  </div>

  <div class="col-md-3">
    <label class="form-label small fw-bold text-muted text-uppercase mb-1">Cari Nama</label>

    {{-- Wrapper Flexbox agar elemen sejajar Horizontal --}}
    <div class="d-flex gap">

      {{-- BAGIAN 1: INPUT PENCARIAN & TOMBOL SEARCH --}}
      <div class="input-group">
        <input type="text" name="keyword" class="form-control" placeholder="Nama peserta..." value="{{ request('keyword') }}">
        <button class="btn btn-primary" type="submit">
          <i class="fas fa-search"></i>
        </button>
      </div>

      {{-- BAGIAN 2: TOMBOL HAPUS (DILUAR INPUT GROUP) --}}
      @if(request('keyword'))
      {{--
         Tips: Tambahkan 'page' ke except agar saat reset search, 
         halaman kembali ke page 1 (mencegah error pagination kosong) 
      --}}
      <a href="{{ route('admin.presensi.index', request()->except(['keyword', 'page'])) }}"
        class="btn btn-outline-secondary shadow-sm"
        title="Hapus Pencarian"
        style="min-width: 40px;"> {{-- Agar tombol tidak gepeng --}}
        <i class="fas fa-times"></i>
      </a>
      @endif

    </div>
  </div>

</form>