<div class="card border-0 shadow-sm mb-4">
  <div class="card-body">
    <form action="{{ route('admin.reports.attendance.index') }}" method="GET" class="row g-3 align-items-end">

      {{-- Filter Bulan --}}
      <div class="col-md-2">
        <label class="form-label fw-bold small">Bulan</label>
        <select name="bulan" class="form-select">
          @for($i = 1; $i <= 12; $i++)
            <option value="{{ $i }}" {{ $selectedBulan == $i ? 'selected' : '' }}>
            {{ \Carbon\Carbon::create()->month($i)->locale('id')->isoFormat('MMMM') }}
            </option>
            @endfor
        </select>
      </div>

      {{-- Filter Tahun --}}
      <div class="col-md-2">
        <label class="form-label fw-bold small">Tahun</label>
        <select name="tahun" class="form-select">
          @for($y = 2023; $y <= date('Y') + 1; $y++)
            <option value="{{ $y }}" {{ $selectedTahun == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
      </div>

      {{-- Filter Divisi --}}
      <div class="col-md-3">
        <label class="form-label fw-bold small">Divisi</label>
        <select name="lowongan_id" class="form-select">
          <option value="">-- Semua Divisi --</option>
          @foreach($divisiList as $divisi)
          <option value="{{ $divisi->id }}" {{ $selectedDivisi == $divisi->id ? 'selected' : '' }}>
            {{ $divisi->divisi }}
          </option>
          @endforeach
        </select>
      </div>

      {{-- Buttons --}}
      <div class="col-md-5">
        <button type="submit" class="btn btn-primary fw-bold">
          <i class="fas fa-filter me-1"></i> Tampilkan
        </button>
        <a href="{{ route('admin.reports.attendance.pdf', request()->query()) }}" class="btn btn-danger fw-bold" target="_blank">
          <i class="fas fa-file-pdf me-1"></i> PDF
        </a>
      </div>
    </form>
  </div>
</div>