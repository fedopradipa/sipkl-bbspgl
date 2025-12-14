{{--
  Status Badge Component
  Props: $status (string) - pending|diterima|ditolak
--}}

@if($status == 'pending')
<span class="badge bg-warning text-dark bg-opacity-25 border border-warning px-3 py-2 rounded-pill">
  <i class="fas fa-hourglass-half me-1"></i> Menunggu
</span>
@elseif($status == 'diterima')
<span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill">
  <i class="fas fa-check-circle me-1"></i> Diterima
</span>
@else
<span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill">
  <i class="fas fa-times-circle me-1"></i> Ditolak
</span>
@endif