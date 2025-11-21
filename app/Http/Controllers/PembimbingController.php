<?php

namespace App\Http\Controllers;

use App\Models\Pembimbing;
use Illuminate\Http\Request;

class PembimbingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('keyword');
        $perPage = 10;

        $pembimbings = Pembimbing::when($search, function ($query, $search) {
            return $query->where('nama', 'like', "%{$search}%")
                ->orWhere('jabatan', 'like', "%{$search}%");
        })
            ->latest()
            ->paginate($perPage)
            ->appends(['keyword' => $search]);

        return view('admin.pembimbing.index', [
            'pembimbings' => $pembimbings,
            'title'       => 'Data Pembimbing Lapangan',
            'header'      => 'Daftar Pembimbing'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pembimbing.create', [
            'title'  => 'Tambah Pembimbing',
            'header' => 'Tambah Pembimbing Baru'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi Terpusat
        $validatedData = $this->validateRequest($request);

        // 2. Simpan Data
        Pembimbing::create($validatedData);

        return redirect()->route('pembimbing.index')
            ->with('success', 'Pembimbing berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pembimbing $pembimbing) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pembimbing $pembimbing)
    {
        return view('admin.pembimbing.edit', [
            'pembimbing' => $pembimbing,
            'title'      => 'Edit Pembimbing',
            'header'     => 'Edit Pembimbing: ' . $pembimbing->nama
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pembimbing $pembimbing)
    {
        // 1. Validasi Terpusat
        $validatedData = $this->validateRequest($request);

        // 2. Update Data
        $pembimbing->update($validatedData);

        return redirect()->route('pembimbing.index')
            ->with('success', 'Data pembimbing berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pembimbing $pembimbing)
    {
        $pembimbing->delete();

        return redirect()->route('pembimbing.index')
            ->with('success', 'Data pembimbing berhasil dihapus permanen.');
    }

    /**
     * -------------------------------------------------------
     * PRIVATE METHOD: VALIDASI REQUEST
     * -------------------------------------------------------
     */
    private function validateRequest(Request $request)
    {
        // 1. Normalisasi Checkbox (is_active)
        // HTML Checkbox tidak kirim value jika unchecked, kita paksa jadi boolean.
        $request->merge([
            'is_active' => $request->has('is_active'),
        ]);

        // 2. Daftar Aturan
        $rules = [
            'nama'      => 'required|string|max:255',
            'jabatan'   => 'required|string|max:255',
            'no_hp'     => 'nullable|string|max:20', // Gunakan string agar support karakter + atau spasi
            'is_active' => 'boolean',
        ];

        // 3. Pesan Error Bahasa Indonesia
        $messages = [
            'required' => 'Kolom :attribute wajib diisi.',
            'max'      => 'Kolom :attribute maksimal :max karakter.',
            'string'   => 'Kolom :attribute harus berupa teks.',
        ];

        // 4. Eksekusi Validasi
        return $request->validate($rules, $messages);
    }
}
