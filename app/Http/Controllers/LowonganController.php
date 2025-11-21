<?php

namespace App\Http\Controllers;

use App\Models\Lowongan; // Pastikan huruf besar L (Sesuai nama Model)
use Illuminate\Http\Request;

class LowonganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('keyword');
        $perPage = 10;

        $lowongans = Lowongan::when($search, function ($query, $search) {
            return $query->where('judul', 'like', "%{$search}%")
                ->orWhere('divisi', 'like', "%{$search}%");
        })
            ->latest()
            ->paginate($perPage)
            ->appends(['keyword' => $search]);

        return view('admin.lowongan.index', [
            'lowongans' => $lowongans,
            'title'     => 'Manajemen Lowongan',
            'header'    => 'Daftar Lowongan PKL'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.lowongan.create', [
            'title'  => 'Buka Lowongan Baru',
            'header' => 'Buka Lowongan Baru'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Panggil fungsi validasi private di bawah
        $validatedData = $this->validateRequest($request);

        // 2. Simpan data (langsung pakai array hasil validasi)
        Lowongan::create($validatedData);

        return redirect()->route('lowongan.index')
            ->with('success', 'Lowongan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lowongan $lowongan)
    {
        return view('admin.lowongan.show', [
            'lowongan' => $lowongan,
            'title' => 'Detail Lowongan',
            'header' => 'Detail Lowongan: ' . $lowongan->judul
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lowongan $lowongan)
    {
        return view('admin.lowongan.edit', [
            'lowongan' => $lowongan,
            'title' => 'Edit Lowongan',
            'header' => 'Edit Lowongan: ' . $lowongan->judul
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lowongan $lowongan)
    {
        // 1. Panggil fungsi validasi private di bawah
        $validatedData = $this->validateRequest($request);

        // 2. Update data
        $lowongan->update($validatedData);

        return redirect()->route('lowongan.index')
            ->with('success', 'Lowongan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lowongan $lowongan)
    {
        $lowongan->delete();
        return redirect()->route('lowongan.index')
            ->with('success', 'Lowongan berhasil dihapus.');
    }

    /**
     * -------------------------------------------------------
     * PRIVATE METHOD: VALIDASI REQUEST (DRY PRINCIPLE)
     * -------------------------------------------------------
     * Fungsi ini menangani semua logika validasi input
     * agar tidak ditulis ulang di store() dan update().
     */
    private function validateRequest(Request $request)
    {
        // 1. Normalisasi Checkbox
        // Ubah input checkbox menjadi boolean true/false
        $request->merge([
            'is_fulltime' => $request->has('is_fulltime'),
            'is_open' => $request->has('is_open'),
        ]);

        // 2. Daftar Aturan
        $rules = [
            'judul' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
            'kuota' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'window_start_date' => 'required|date',
            'window_end_date' => 'required|date|after_or_equal:window_start_date',
            'min_duration' => 'required|integer|min:1',
            'jam_masuk_standar' => 'required|date_format:H:i',
            'jam_pulang_standar' => 'required|date_format:H:i|after:jam_masuk_standar',
            // Pastikan field boolean divalidasi juga agar masuk ke $validatedData
            'is_fulltime' => 'boolean',
            'is_open' => 'boolean',
        ];

        // 3. Pesan Error Bahasa Indonesia
        $messages = [
            'required' => 'Kolom :attribute wajib diisi.',
            'min' => 'Nilai :attribute minimal :min.',
            'date_format' => 'Format waktu :attribute harus Jam:Menit (Contoh: 08:00).',
            'window_end_date.after_or_equal' => 'Tanggal penutupan pendaftaran tidak boleh lebih awal dari tanggal pembukaan.',
            'jam_pulang_standar.after' => 'Jam pulang harus lebih akhir dari jam masuk.',
        ];

        // 4. Jalankan Validasi
        return $request->validate($rules, $messages);
    }
}
