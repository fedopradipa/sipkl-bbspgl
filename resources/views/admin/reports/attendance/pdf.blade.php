<!DOCTYPE html>
<html>

<head>
  <title>Laporan Kehadiran Peserta Magang</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 10px;
      line-height: 1.4;
    }

    .header {
      text-align: center;
      margin-bottom: 20px;
      border-bottom: 2px solid #333;
      padding-bottom: 10px;
    }

    .header h1 {
      margin: 0;
      font-size: 18px;
      color: #0d47a1;
    }

    .header p {
      margin: 0;
      font-size: 10px;
    }

    .meta-info {
      margin-bottom: 20px;
      border: 1px solid #ddd;
      padding: 10px;
      background-color: #f9f9f9;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th,
    td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
      text-align: center;
      font-weight: bold;
    }

    .text-center {
      text-align: center;
    }

    .text-right {
      text-align: right;
    }

    .footer {
      margin-top: 50px;
      page-break-inside: avoid;
    }

    .signature-box {
      width: 45%;
      float: right;
      text-align: center;
      margin-right: 0;
    }
  </style>
</head>

<body>

  {{-- KOP SURAT --}}
  <div class="header">
    <h1>LAPORAN KEHADIRAN PESERTA MAGANG</h1>
    <p>Balai Besar Survei Pemetaan dan Geologi Kelautan (BBSPGL)</p>
  </div>

  {{-- METADATA --}}
  <div class="meta-info">
    <strong>Periode Laporan:</strong>
    {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}
    <br>
    {{-- MENGGUNAKAN VARIABEL GLOBAL BARU --}}
    <strong>Total Hari Kerja (Senin-Jumat) dalam Periode:</strong> {{ $totalEffectiveDaysGlobal }} Hari
    <br>
    <small>*Hari Efektif di tabel dihitung berdasarkan jadwal kerja resmi setiap peserta.</small>
  </div>

  {{-- ISI TABEL --}}
  <table>
    <thead>
      <tr>
        <th>Nama Peserta & Divisi</th>
        <th class="text-center">Hadir (H)</th>
        <th class="text-center">Sakit (S)</th>
        <th class="text-center">Izin (I)</th>
        <th class="text-center">Alpha (A)</th>
        <th class="text-center">Total Terlambat</th>
        <th class="text-center">Hari Efektif (Jadwal)</th>
        <th class="text-right">Persentase (%)</th>
      </tr>
    </thead>
    <tbody>
      @foreach($rekapAbsensi as $rekap)
      <tr>
        <td>
          <strong>{{ $rekap->nama_peserta }}</strong><br>
          <small>{{ $rekap->divisi_peserta }}</small>
        </td>
        <td class="text-center">{{ $rekap->total_hadir_terlambat }}</td>
        <td class="text-center">{{ $rekap->total_sakit }}</td>
        <td class="text-center">{{ $rekap->total_izin }}</td>
        <td class="text-center">{{ $rekap->total_alpha }}</td>
        <td class="text-center">{{ $rekap->total_terlambat }}</td>

        {{-- MENGGUNAKAN HARI EFEKTIF INDIVIDUAL --}}
        <td class="text-center">{{ $rekap->total_days }}</td>

        <td class="text-right"><strong>{{ $rekap->persentase }}%</strong></td>
      </tr>
      @endforeach
    </tbody>
  </table>

  {{-- FOOTER TANDA TANGAN --}}
  <div class="footer">
    {{-- ... (kode tanda tangan) ... --}}
  </div>

</body>

</html>