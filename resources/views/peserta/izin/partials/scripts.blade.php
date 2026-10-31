@php
// Definisikan variabel PHP di sini agar aman dari auto-formatter JS
$minDate = $lamaran->tgl_mulai_resmi;
$maxDate = $lamaran->tgl_selesai_resmi;
$hariKerja = $lamaran->hari_kerja_resmi;

// Logika existing dates
$tanggalExisting = isset($izin) ? $izin->tanggal_izin : [];
@endphp

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // 1. Ambil Batas Tanggal PKL (Panggil variabel yg sudah didefine di atas)
    const minPkl = "{{ $minDate }}";
    const today = new Date(); // hari ini (client)
    today.setHours(0, 0, 0, 0);

    // minDate final = max(tgl_mulai_resmi, today)
    const minDate = (new Date(minPkl) > today) ? minPkl : "today";

    const maxDate = "{{ $maxDate }}";

    // 2. Data Hari Kerja
    const dataHariKerja = @json($hariKerja);

    // 3. Data Tanggal Existing (Hanya untuk Edit)
    const existingDates = @json($tanggalExisting);

    const mapHari = {
      'Minggu': 0,
      'Senin': 1,
      'Selasa': 2,
      'Rabu': 3,
      'Kamis': 4,
      'Jumat': 5,
      'Sabtu': 6
    };

    const allowedDays = Array.isArray(dataHariKerja) ?
      dataHariKerja.map(hari => mapHari[hari]) : [1, 2, 3, 4, 5];

    flatpickr("#tanggal_picker", {
      mode: "multiple",
      dateFormat: "Y-m-d",
      minDate: minDate,
      maxDate: maxDate,
      locale: "id",
      defaultDate: existingDates, // <--- KUNCI EDIT: Load tanggal yang tersimpan
      disable: [
        function(date) {
          return !allowedDays.includes(date.getDay());
        }
      ]
    });
  });
</script>