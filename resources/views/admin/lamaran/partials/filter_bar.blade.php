<form action="{{ route('admin.lamaran.index') }}" method="GET" class="d-flex flex-column flex-md-row gap-2 w-100">

  {{-- FILTER 1: STATUS TAB --}}
  {{-- UX UPDATE: Hapus 'diterima' & 'all'. Fokus hanya Pending (Inbox) & Ditolak (History) --}}
  <div class="btn-group shadow-sm">
    @foreach(['pending', 'ditolak'] as $st)
    <a href="{{ route('admin.lamaran.index', array_merge(request()->query(), ['status' => $st])) }}"
      class="btn btn-sm {{ $currentStatus == $st ? 'btn-primary' : 'btn-white border' }} text-capitalize px-3">

      {{-- Ikon Pemanis --}}
      @if($st == 'pending') <i class="fas fa-inbox me-1"></i>
      @elseif($st == 'ditolak') <i class="fas fa-history me-1"></i>
      @endif

      {{ $st }}
    </a>
    @endforeach
  </div>

  {{-- Wrapper untuk Filter Kanan --}}
  <div class="d-flex gap-2 ms-md-auto">

    {{-- FILTER 2: LOWONGAN --}}
    <select name="lowongan_id"
      class="form-select form-select-sm shadow-sm border-0 bg-white"
      style="max-width: 200px; cursor: pointer;"
      onchange="this.form.submit()">
      <option value="">Semua Posisi</option>
      {{-- Gunakan variabel $lowongans dari Controller --}}
      @foreach($lowongans as $l)
      <option value="{{ $l->id }}" {{ request('lowongan_id') == $l->id ? 'selected' : '' }}>
        {{ $l->judul }}
      </option>
      @endforeach
    </select>

    {{-- FILTER 3: PENCARIAN --}}
    <div class="input-group input-group-sm shadow-sm" style="max-width: 250px;">
      <input type="text"
        name="keyword"
        class="form-control border-0"
        placeholder="Cari pelamar..."
        value="{{ request('keyword') }}">

      <button class="btn btn-white border-0 text-primary bg-white" type="submit">
        <i class="fas fa-search"></i>
      </button>

      @if(request('keyword'))
      <a href="{{ route('admin.lamaran.index', ['status' => $currentStatus]) }}"
        class="btn btn-white border-0 text-danger bg-white"
        title="Reset Cari">
        <i class="fas fa-times"></i>
      </a>
      @endif
    </div>
  </div>
</form>