<!DOCTYPE html>
<html>

<head>
  <title>Laporan Absensi - {{ $bulanNama }} {{ $tahun }}</title>
  <style>
    /* ====================================================
           1. PAGE SETUP (LANDSCAPE)
           ==================================================== */
    @page {
      margin: 10mm 10mm 10mm 10mm;
      size: A4 landscape;
    }

    body {
      font-family: sans-serif;
      font-size: 9px;
      color: #333;
    }

    /* ====================================================
           2. COLOR MAPPING
           ==================================================== */
    .header-main {
      background-color: #003366;
      color: white;
    }

    .header-date {
      background-color: #004080;
      color: white;
    }

    .bg-alpha {
      background-color: #ff4444;
      color: white;
    }

    .bg-libur {
      background-color: #ff9900;
      color: white;
    }

    .bg-sakit {
      background-color: #ffeb3b;
      color: #333;
    }

    .bg-izin {
      background-color: #ffeb3b;
      color: #333;
    }

    .bg-belum {
      background-color: #e0e0e0;
      color: #666;
    }

    /* Warna Abu-abu */

    .total-h {
      background-color: #d4edda;
      color: #155724;
    }

    .total-si {
      background-color: #fff3cd;
      color: #856404;
    }

    .total-a {
      background-color: #f8d7da;
      color: #721c24;
    }

    .total-t {
      background-color: #d1ecf1;
      color: #0c5460;
    }

    /* ====================================================
           3. TABLE STYLING
           ==================================================== */
    table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;
      margin-bottom: 20px;
    }

    th,
    td {
      border: 0.5px solid #444;
      padding: 3px 2px;
      text-align: center;
      vertical-align: middle;
      font-size: 8px;
      line-height: 1.1;
      word-wrap: break-word;
    }

    /* Kolom Nama: Wrap Text (Turun ke bawah) */
    .col-nama {
      text-align: left;
      padding-left: 4px;
      white-space: normal;
      word-wrap: break-word;
    }

    /* Styling Jam */
    .waktu-container {
      display: block;
    }

    .waktu-telat {
      color: #dc3545;
      font-weight: bold;
    }

    .waktu-normal {
      color: #0066cc;
    }

    .label-telat {
      font-size: 6px;
      color: #dc3545;
      display: block;
      margin-top: 1px;
    }

    .badge-mini {
      font-weight: bold;
      font-size: 8px;
    }

    .doc-header {
      text-align: center;
      margin-bottom: 15px;
    }

    .doc-header h2 {
      margin: 0;
      font-size: 16px;
      text-transform: uppercase;
      color: #003366;
    }

    .doc-header p {
      margin: 2px 0;
      font-size: 11px;
    }
  </style>
</head>

<body>

  {{-- HEADER DOKUMEN --}}
  <div class="doc-header">
    <h2>Laporan Rekapitulasi Presensi</h2>
    <p>Periode: <strong>{{ $bulanNama }} {{ $tahun }}</strong></p>
  </div>

  {{-- TABEL --}}
  <table>
    <thead>
      {{-- Baris 1: Header Utama --}}
      <tr class="header-main">
        <th rowspan="2" style="width: 20px;">NO</th>
        <th rowspan="2" style="width: 120px;">NAMA</th>
        <th rowspan="2" style="width: 60px;">DIVISI</th>

        @foreach($dates as $ymd => $info)
        <th>{{ substr(strtoupper($info['hari']), 0, 1) }}</th>
        @endforeach
        <th colspan="4" style="width: 80px;">TOTAL</th>
      </tr>
      {{-- Baris 2: Tanggal Angka --}}
      <tr class="header-date">
        @foreach($dates as $ymd => $info)
        <th>{{ str_pad($info['tgl'], 2, '0', STR_PAD_LEFT) }}</th>
        @endforeach
        <th class="total-h">H</th>
        <th class="total-si">S/I</th>
        <th class="total-a">A</th>
        <th class="total-t">T</th>
      </tr>
    </thead>
    <tbody>
      @foreach($rekapData as $index => $data)
      <tr>
        <td>{{ $loop->iteration }}</td>

        {{-- KOLOM NAMA --}}
        <td class="col-nama">
          <strong>{{ $data['nama'] }}</strong>
        </td>

        {{-- KOLOM DIVISI --}}
        <td style="font-size: 7px;">{{ $data['divisi'] }}</td>

        {{-- LOOP DATA HARIAN --}}
        @foreach($dates as $ymd => $info)
        @php
        $log = $data['harian'][$ymd];
        $status = $log['status'];
        $class = '';
        $content = '';

        if ($status == 'alpha') {
        $class = 'bg-alpha';
        $content = '<span class="badge-mini">A</span>';
        }
        elseif ($status == 'libur' || $status == 'off_period') {
        $class = 'bg-libur';
        $content = '';
        }
        elseif ($status == 'sakit') {
        $class = 'bg-sakit';
        $content = '<span class="badge-mini">S</span>';
        }
        elseif ($status == 'izin') {
        $class = 'bg-izin';
        $content = '<span class="badge-mini">I</span>';
        }
        elseif ($status == 'hadir' || $status == 'terlambat') {
        $masuk = $log['jam_masuk'] ? substr($log['jam_masuk'], 0, 5) : '-';
        $pulang = $log['jam_pulang'] ? substr($log['jam_pulang'], 0, 5) : '-';
        $clsM = ($status == 'terlambat') ? 'waktu-telat' : 'waktu-normal';

        $content = "<div class='waktu-container'>
          <span class='$clsM'>$masuk</span><br>
          <span class='waktu-normal'>$pulang</span>
        </div>";
        if ($status == 'terlambat') {
        $content .= "<span class='label-telat'>Telat</span>";
        }
        }
        elseif ($status == 'belum' || $status == '-') {
        $class = 'bg-belum';
        $content = '-';
        }
        @endphp

        <td class="{{ $class }}">
          {!! $content !!}
        </td>
        @endforeach

        {{-- TOTAL --}}
        <td class="total-h"><strong>{{ $data['total']['hadir'] + $data['total']['terlambat'] }}</strong></td>
        <td class="total-si">{{ $data['total']['sakit'] + $data['total']['izin'] }}</td>
        <td class="total-a"><strong>{{ $data['total']['alpha'] }}</strong></td>
        <td class="total-t">{{ $data['total']['terlambat'] }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  {{-- FOOTER --}}
  <table style="width: 100%; border: none; margin-top: 10px;">
    <tr style="border: none;">
      {{-- KETERANGAN WARNA (LEGENDA) --}}
      <td style="border: none; text-align: left; vertical-align: top; width: 60%;">
        <strong>Keterangan Warna:</strong><br>

        {{-- ITEM 1: HADIR --}}
        <span style="display:inline-block; width:10px; height:10px; background:#fff; border:1px solid #ccc; margin-right:5px; vertical-align:middle;"></span> Hadir &nbsp;

        {{-- ITEM 2: SAKIT/IZIN --}}
        <span style="display:inline-block; width:10px; height:10px; background:#ffeb3b; margin-right:5px; vertical-align:middle; border:1px solid #ccc;"></span> Sakit/Izin (S/I) &nbsp;

        {{-- ITEM 3: ALPHA --}}
        <span style="display:inline-block; width:10px; height:10px; background:#ff4444; margin-right:5px; vertical-align:middle;"></span> Alpha (A) &nbsp;

        {{-- ITEM 4: LIBUR --}}
        <span style="display:inline-block; width:10px; height:10px; background:#ff9900; margin-right:5px; vertical-align:middle;"></span> Diluar Jadwal Kerja &nbsp;

        {{-- ITEM 5: BELUM ABSEN (TAMBAHAN BARU) --}}
        <span style="display:inline-block; width:10px; height:10px; background:#e0e0e0; margin-right:5px; vertical-align:middle; border:1px solid #ccc;"></span> Belum Absen
      </td>

      {{-- TANDA TANGAN --}}
      <td style="border: none; text-align: center; vertical-align: top;">
        <p>Jakarta, {{ date('d') }} {{ $bulanNama }} {{ $tahun }}</p>
        <p>Mengetahui,</p>
        <br><br><br>
        <p style="text-decoration: underline; font-weight: bold;">( _______________________ )</p>
        <p>HRD / Pimpinan</p>
      </td>
    </tr>
  </table>

</body>

</html>