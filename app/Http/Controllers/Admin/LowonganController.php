<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use App\Http\Requests\LowonganRequest; // Import Request
use App\Services\LowonganService;      // Import Service
use Illuminate\Http\Request;

class LowonganController extends Controller
{
    protected $lowonganService;

    // Dependency Injection Service
    public function __construct(LowonganService $lowonganService)
    {
        $this->lowonganService = $lowonganService;
    }

    public function index(Request $request)
    {
        $search = $request->input('keyword');

        $lowongans = Lowongan::when($search, function ($query, $search) {
            return $query->where('judul', 'like', "%{$search}%")
                ->orWhere('divisi', 'like', "%{$search}%");
        })
            ->latest()
            ->paginate(10)
            ->appends(['keyword' => $search]);

        return view('admin.lowongan.index', [
            'lowongans' => $lowongans,
            'title'     => 'Manajemen Lowongan',
            'header'    => 'Daftar Lowongan PKL'
        ]);
    }

    public function create()
    {
        return view('admin.lowongan.create', [
            'title'  => 'Buka Lowongan Baru',
            'header' => 'Buka Lowongan Baru'
        ]);
    }

    // Gunakan LowonganRequest, bukan Request biasa
    public function store(LowonganRequest $request)
    {
        // Data yang masuk ke sini SUDAH TERVALIDASI & SUDAH DINORMALISASI
        // oleh method prepareForValidation() di Request.

        $this->lowonganService->createLowongan($request->validated());

        return redirect()->route('lowongan.index')
            ->with('success', 'Lowongan berhasil ditambahkan.');
    }

    public function show(Lowongan $lowongan)
    {
        return view('admin.lowongan.show', [
            'lowongan' => $lowongan,
            'title'    => 'Detail Lowongan',
            'header'   => 'Detail Lowongan: ' . $lowongan->judul
        ]);
    }

    public function edit(Lowongan $lowongan)
    {
        return view('admin.lowongan.edit', [
            'lowongan' => $lowongan,
            'title'    => 'Edit Lowongan',
            'header'   => 'Edit Lowongan: ' . $lowongan->judul
        ]);
    }

    // Gunakan LowonganRequest untuk update juga
    public function update(LowonganRequest $request, Lowongan $lowongan)
    {
        $this->lowonganService->updateLowongan($lowongan, $request->validated());

        return redirect()->route('lowongan.index')
            ->with('success', 'Lowongan berhasil diperbarui.');
    }

    public function destroy(Lowongan $lowongan)
    {
        $lowongan->delete();
        return redirect()->route('lowongan.index')
            ->with('success', 'Lowongan berhasil dihapus.');
    }
}
