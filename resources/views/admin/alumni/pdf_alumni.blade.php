<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Laporan Rekapitulasi Peserta PKL</title>
  <style>
    body {
      font-family: sans-serif;
      font-size: 11px;
    }

    .header {
      text-align: center;
      margin-bottom: 20px;
      border-bottom: 2px solid #000;
      padding-bottom: 10px;
    }

    .header h2 {
      margin: 0;
      text-transform: uppercase;
      font-size: 16px;
    }

    .header p {
      margin: 2px 0;
      font-size: 12px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th,
    td {
      border: 1px solid #000;
      padding: 6px;
      text-align: left;
      vertical-align: top;
    }

    th {
      background-color: #f0f0f0;
      font-weight: bold;
      text-transform: uppercase;
      font-size: 10px;
    }

    .text-center {
      text-align: center;
    }

    .badge-lulus {
      color: green;
      font-weight: bold;
    }

    .badge-do {
      color: red;
      font-weight: bold;
    }

    .text-muted {
      color: #666;
      font-size: 10px;
      font-style: italic;
    }

    tr {
      page-break-inside: avoid;
    }
  </style>
</head>

<body>

  <div class="header">
    <h2>LAPORAN REKAPITULASI PESERTA PKL</h2>
    <p>BBSPGL (Balai Besar Survei Pemetaan dan Geologi Kelautan)</p>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width: 5%" class="text-center">No</th>
        <th style="width: 30%">Peserta & Institusi</th>
        <th style="width: 25%">Posisi PKL</th>
        <th style="width: 20%">Periode Pelaksanaan</th>
        <th style="width: 20%">Status Akhir</th>
      </tr>
    </thead>

    <tbody>
      @foreach($data as $index => $item)
      @php
      $mulai = \Carbon\Carbon::parse($item->tgl_mulai_resmi ?? $item->tgl_mulai_usulan);
      $selesai = \Carbon\Carbon::parse($item->tgl_selesai_resmi ?? $item->tgl_selesai_usulan);
      @endphp

      <tr>
        <td class="text-center">{{ $index + 1 }}</td>

        <td>
          <strong>{{ $item->pengguna->name }}</strong><br>
          {{ optional($item->pengguna->profile)->institusi ?? '-' }}<br>
          <span class="text-muted">{{ optional($item->pengguna->profile)->jurusan ?? '' }}</span>
        </td>

        <td>
          {{ $item->lowongan->posisi ?? '-' }}
        </td>

        <td>
          {{ $mulai->format('d/m/Y') }} s/d<br>
          {{ $selesai->format('d/m/Y') }}
        </td>

        <td>
          @if($item->status == 'selesai')
          <span class="badge-lulus">LULUS</span>
          @else
          <span class="badge-do">DROP OUT</span><br>
          <span class="text-muted">Ket: {{ $item->catatan_admin ?? '-' }}</span>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

</body>

</html>