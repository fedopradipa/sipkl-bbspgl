<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePresensiRequest;
use Illuminate\Http\Request;
use App\Services\Admin\PresensiAdminService; // Import Service
use App\Models\Presensi;
use App\Models\Lamaran;
use App\Models\Lowongan;

use Carbon\Carbon;

class PresensiAdminController extends Controller
{

    protected $presensiService;

    public function __construct(PresensiAdminService $presensiService)
    {
        $this->presensiService = $presensiService;
    }
    /**
     * Monitoring Presensi Harian (Advanced Logic)
     */
    public function index(Request $request)
    {
        // 1. SETUP PARAMETER
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $divisiId = $request->input('divisi_id');
        $statusFilter = $request->input('status'); // hadir, terlambat, alpha, dll
        $keyword = $request->input('keyword');

        // 2. TENTUKAN HARI (Untuk Cek Jadwal Kerja)
        // Carbon format('l') menghasilkan "Monday", kita butuh "Senin"
        $mapHari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $hariIni = $mapHari[Carbon::parse($tanggal)->format('l')];

        // 3. QUERY MASTER: AMBIL SEMUA PESERTA YANG "SEHARUSNYA" MASUK
        // Kita query dari tabel LAMARAN, bukan Presensi.
        $query = Lamaran::with(['pengguna', 'lowongan', 'presensi' => function ($q) use ($tanggal) {
            // Eager Load: Ambil data presensi HANYA di tanggal yang dipilih
            $q->whereDate('tanggal', $tanggal);
        }])
            ->where('status', 'diterima')
            // Cek Periode Magang (Masih Aktif)
            ->whereDate('tgl_mulai_resmi', '<=', $tanggal)
            ->whereDate('tgl_selesai_resmi', '>=', $tanggal)
            // CEK JADWAL: Pastikan hari ini ada di array jadwal mereka
            // Asumsi kolom 'hari_kerja_resmi' di DB tipe JSON atau Text yang di-cast array
            ->whereJsonContains('hari_kerja_resmi', $hariIni);

        // Filter Divisi
        if ($divisiId) {
            $query->whereHas('lowongan', function ($q) use ($divisiId) {
                $q->where('id', $divisiId);
            });
        }

        // Filter Keyword (Nama)
        if ($keyword) {
            $query->whereHas('pengguna', function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%");
            });
        }

        // Eksekusi Query
        $pesertaWajibHadir = $query->get();

        // 4. MAPPING DATA (GABUNGKAN DATA PESERTA + DATA ABSEN)
        // Kita ubah collection Lamaran menjadi format data presensi untuk View
        $finalData = $pesertaWajibHadir->map(function ($item) use ($tanggal) {
            // Ambil data presensi dari eager loading tadi (bisa null jika belum absen)
            $absen = $item->presensi->first();

            // Tentukan Status UI
            if ($absen) {
                // Jika sudah absen, pakai status dari DB (hadir/terlambat/sakit/izin)
                $status = $absen->status;
                $jamMasuk = $absen->jam_masuk;
                $jamPulang = $absen->jam_pulang;
                $catatan = $absen->catatan_harian;
                $presensiId = $absen->id; // Untuk tombol edit/hapus
            } else {
                // Jika BELUM absen (Data presensi null)
                // Cek apakah jam sekarang sudah lewat jam pulang default (misal 17:00)?
                // Jika lewat hari, anggap Alpha. Jika masih hari ini, anggap Belum Absen.
                $isHariLewat = Carbon::now()->gt(Carbon::parse($tanggal)->endOfDay());

                $status = $isHariLewat ? 'alpha' : 'belum_absen';
                $jamMasuk = null;
                $jamPulang = null;
                $catatan = '-';
                $presensiId = null;
            }

            // Kembalikan objek standar agar mudah dipakai di Blade
            return (object) [
                'id' => $presensiId, // ID tabel presensi (bisa null)
                'lamaran' => $item,  // Relasi ke data peserta
                'tanggal' => Carbon::parse($tanggal),
                'jam_masuk' => $jamMasuk,
                'jam_pulang' => $jamPulang,
                'status' => $status, // hadir, terlambat, sakit, izin, alpha, belum_absen
                'catatan_harian' => $catatan,
                'is_auto_closed' => $absen ? $absen->is_auto_closed : false,
            ];
        });

        // 5. FILTER STATUS (MANUAL COLLECTION FILTER)
        // Karena status 'belum_absen' atau 'alpha' hasil kalkulasi di atas tidak ada di DB,
        // kita filter manual collection-nya.
        if ($statusFilter) {
            $finalData = $finalData->where('status', $statusFilter);
        }

        // 6. HITUNG RINGKASAN (STATS)
        // Hitung dari $finalData yang sudah diproses (sebelum difilter status, idealnya)
        // Tapi untuk performa, kita hitung dari hasil mapping full (sebelum if step 5)
        // *Note: Agar akurat, step 5 sebaiknya dilakukan setelah step 6, atau clone collection.
        // Disini saya taruh logic ringkasan SAMA dengan UI yang tampil.

        $ringkasan = [
            'hadir'      => $finalData->where('status', 'hadir')->count(),
            'terlambat'  => $finalData->where('status', 'terlambat')->count(),
            'sakit_izin' => $finalData->whereIn('status', ['sakit', 'izin'])->count(),
            'alpha'      => $finalData->whereIn('status', ['alpha', 'belum_absen'])->count(), // Gabung alpha & belum absen
        ];

        // 7. DATA PENDUKUNG VIEW
        // Ambil list divisi untuk dropdown filter
        $divisi_list = Lowongan::select('id', 'judul', 'divisi')->distinct()->get();
        // Ambil data peserta aktif untuk Dropdown Modal Input Manual
        // Syarat: Status diterima DAN Tanggal filter masuk dalam periode magang mereka
        $pesertaAktif = Lamaran::with(['pengguna', 'lowongan'])
            ->where('status', 'diterima')
            ->whereDate('tgl_mulai_resmi', '<=', $tanggal)
            ->whereDate('tgl_selesai_resmi', '>=', $tanggal)
            ->get();
        return view('admin.presensi.index', [
            'presensis' => $finalData, // Ini Collection, bukan Paginator (bisa dipaginate manual jika mau)
            'ringkasan' => $ringkasan,
            'tanggal'   => $tanggal,
            'divisi_list' => $divisi_list,
            'selected_divisi' => $divisiId,
            'pesertaAktif'    => $pesertaAktif,
            'title'   => 'Presensi Harian Peseerta'
        ]);
    }

    // =================================================================
    // FITUR ADMIN: CRUD PRESENSI (FIXED LOGIC)
    // =================================================================

    /**
     * SIMPAN DATA MANUAL (Input Admin)
     * Route: POST /presensi/store
     */
    public function storeAdmin(StorePresensiRequest $request)
    {
        try {
            $this->presensiService->storeManual($request->validated());
            return back()->with('success', 'Data presensi berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateAdmin(Request $request, $id)
    {
        // Validasi ID manual karena update ini unik (lewat hidden input)
        $request->validate([
            'status'    => 'required|in:hadir,terlambat,sakit,izin,alpha',
            'jam_masuk' => 'nullable|required_if:status,hadir,terlambat',
            'catatan_harian' => 'nullable|string|max:255'
        ]);

        try {
            $this->presensiService->updateManual($request->id, $request->all());
            return back()->with('success', 'Data presensi berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        Presensi::findOrFail($id)->delete();
        return back()->with('success', 'Data presensi dihapus.');
    }
}
