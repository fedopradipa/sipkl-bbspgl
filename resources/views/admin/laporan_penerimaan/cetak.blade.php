<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Laporan Penerimaan PKL</title>
  <style>
    /* CSS KHUSUS DOMPDF */
    body {
      font-family: "Times New Roman", serif;
      font-size: 12px;
      margin: 0;
      padding: 0;
      line-height: 1.4;
    }

    /* HEADER / KOP SURAT */
    .header {
      text-align: center;
      margin-bottom: 20px;
      border-bottom: 3px double #000;
      padding-bottom: 10px;
    }

    .header h2 {
      margin: 0;
      font-size: 16px;
      text-transform: uppercase;
    }

    .header p {
      margin: 0;
      font-size: 12px;
    }

    /* JUDUL LAPORAN */
    .title {
      text-align: center;
      margin-bottom: 20px;
    }

    .title h3 {
      margin: 0;
      text-decoration: underline;
      text-transform: uppercase;
      font-size: 14px;
    }

    .title p {
      margin: 5px 0 0 0;
      font-size: 12px;
    }

    /* TABEL DATA */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 10px;
    }

    table,
    th,
    td {
      border: 1px solid #000;
    }

    th {
      background-color: #f0f0f0;
      padding: 8px;
      text-align: center;
      font-weight: bold;
    }

    td {
      padding: 6px 8px;
      vertical-align: top;
    }

    /* FOOTER / TANDA TANGAN */
    /* Menggunakan float karena flexbox kurang support di DOMPDF versi lama */
    .footer-container {
      margin-top: 40px;
      width: 100%;
    }

    .ttd-box {
      float: right;
      width: 250px;
      text-align: center;
    }

    .signature-line {
      margin-top: 70px;
      border-bottom: 1px solid #000;
    }
  </style>
</head>

<body>

  {{-- 1. KOP SURAT --}}
  <div class="header">
    <h2>Balai Besar Survei Pemetaan dan Geologi Kelautan Cirebon</h2>

  </div>

  {{-- 2. JUDUL --}}
  <div class="title">
    <h3>REKAPITULASI PENERIMAAN PESERTA MAGANG (PKL)</h3>
    <p>Periode: {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}</p>
  </div>

  {{-- 3. TABEL --}}
  <table>
    <thead>
      <tr>
        <th style="width: 5%">No</th>
        <th style="width: 25%">Nama Peserta</th>
        <th style="width: 20%">Posisi</th>
        <th style="width: 20%">Pembimbing</th>
        <th style="width: 30%">Waktu Pelaksanaan</th>
      </tr>
    </thead>
    <tbody>
      @forelse($laporan as $key => $data)
      <tr>
        <td style="text-align: center;">{{ $key + 1 }}</td>
        <td>
          <strong>{{ $data->pengguna->name ?? $data->pengguna->nama }}</strong>
          <br><span style="font-size: 11px; color: #555;">{{ $data->pengguna->email }}</span>

          {{-- Status text agar terlihat di PDF --}}
          <br>

        </td>
        <td>
          {{ optional($data->lowongan)->posisi ?? '-' }}
        </td>
        <td>{{ $data->pembimbing->nama ?? '-' }}</td>
        <td style="text-align: center;">
          {{ \Carbon\Carbon::parse($data->tgl_mulai_resmi)->format('d/m/Y') }} - <br>
          {{ \Carbon\Carbon::parse($data->tgl_selesai_resmi)->format('d/m/Y') }}
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="5" style="text-align: center; padding: 20px; font-style: italic;">
          Tidak ada data peserta yang diterima pada periode ini.
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>



</body>

</html>