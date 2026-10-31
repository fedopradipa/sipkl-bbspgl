<div class="table-scroll-wrapper">
  <table class="attendance-matrix-table">
    {{-- HEADER --}}
    <thead>
      <tr class="header-main">
        <th rowspan="2" class="col-sticky col-no">NO</th>
        <th rowspan="2" class="col-sticky col-nama">NAMA</th>
        <th rowspan="2" class="col-posisi">posisi</th>
        @foreach($dates as $ymd => $info)
        <th class="col-hari">{{ strtoupper($info['hari']) }}</th>
        @endforeach
        <th colspan="4" class="col-total-group">TOTAL</th>
      </tr>
      <tr class="header-date">
        @foreach($dates as $ymd => $info)
        <th class="col-tanggal">{{ str_pad($info['tgl'], 2, '0', STR_PAD_LEFT) }}</th>
        @endforeach
        <th class="total-h">H</th>
        <th class="total-si">S/I</th>
        <th class="total-a">A</th>
        <th class="total-t">T</th>
      </tr>
    </thead>

    {{-- BODY --}}
    <tbody>
      @forelse($rekapData as $index => $data)
      <tr>
        {{-- Kolom Sticky --}}
        <td class="col-sticky col-no"><b>{{ $loop->iteration }}</b></td>
        <td class="col-sticky col-nama">
          <div class="nama-truncate" title="{{ $data['nama'] }}">{{ $data['nama'] }}</div>
        </td>

        <td class="col-posisi">{{ $data['posisi'] }}</td>

        {{-- Loop Tanggal --}}
        @foreach($dates as $ymd => $info)
        @php
        $log = $data['harian'][$ymd];
        $status = $log['status'];
        $class = '';
        $content = '';

        if ($status == 'alpha') {
        $class = 'status-alpha';
        $content = '<span class="badge-cell">A</span>';
        } elseif ($status == 'libur' || $status == 'off_period') {
        $class = 'status-libur';
        $content = '';
        } elseif ($status == 'sakit') {
        $class = 'status-sakit';
        $content = '<span class="badge-cell">S</span>';
        } elseif ($status == 'izin') {
        $class = 'status-izin';
        $content = '<span class="badge-cell">I</span>';
        } elseif ($status == 'hadir' || $status == 'terlambat') {
        $masuk = $log['jam_masuk'] ? substr($log['jam_masuk'], 0, 5) : '-';
        $clsM = $status == 'terlambat' ? 'waktu-telat' : 'waktu-normal';

        $content = "<div class='waktu-container'><span class='$clsM'>$masuk</span></div>";

        if ($status == 'terlambat') {
        $content .= "<span class='label-telat'>Telat</span>";
        }
        } elseif ($status == 'belum' || $status == '-') {
        $class = 'status-belum';
        $content = '-';
        } else {
        $content = '<span class="badge-cell">' . strtoupper(substr($status, 0, 3)) . '</span>';
        }
        @endphp

        <td class="{{ $class }}">{!! $content !!}</td>
        @endforeach

        {{-- Total --}}
        <td class="total-h"><b>{{ $data['total']['hadir'] + $data['total']['terlambat'] }}</b></td>
        <td class="total-si">{{ $data['total']['sakit'] + $data['total']['izin'] }}</td>
        <td class="total-a"><b>{{ $data['total']['alpha'] }}</b></td>
        <td class="total-t">{{ $data['total']['terlambat'] }}</td>
      </tr>
      @empty
      <tr>
        <td colspan="50" class="py-5">Data tidak ditemukan.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>