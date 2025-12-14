{{-- Main Info Card with Tabs --}}
<div class="card border-0 shadow-sm mb-4">

  {{-- Tab Navigation --}}
  <div class="card-header bg-white border-bottom">
    <ul class="nav nav-tabs card-header-tabs mb-0" role="tablist">
      <li class="nav-item">
        <button
          class="nav-link active"
          data-bs-toggle="tab"
          data-bs-target="#tab-placement"
          type="button">
          <i class="fas fa-briefcase me-2"></i>Detail Penempatan
        </button>
      </li>
      <li class="nav-item">
        <button
          class="nav-link"
          data-bs-toggle="tab"
          data-bs-target="#tab-documents"
          type="button">
          <i class="fas fa-folder-open me-2"></i>Dokumen
        </button>
      </li>
      <li class="nav-item">
        <button
          class="nav-link"
          data-bs-toggle="tab"
          data-bs-target="#tab-timeline"
          type="button">
          <i class="fas fa-history me-2"></i>Timeline
        </button>
      </li>
    </ul>
  </div>

  {{-- Tab Content --}}
  <div class="card-body p-4">
    <div class="tab-content">

      {{-- TAB 1: Placement Details --}}
      <div class="tab-pane fade show active" id="tab-placement">
        @include('admin.peserta_aktif.components.show.tabs.placement', ['item' => $item])
      </div>

      {{-- TAB 2: Documents --}}
      <div class="tab-pane fade" id="tab-documents">
        @include('admin.peserta_aktif.components.show.tabs.documents', ['item' => $item])
      </div>

      {{-- TAB 3: Timeline --}}
      <div class="tab-pane fade" id="tab-timeline">
        @include('admin.peserta_aktif.components.show.tabs.timeline', ['item' => $item])
      </div>

    </div>
  </div>
</div>