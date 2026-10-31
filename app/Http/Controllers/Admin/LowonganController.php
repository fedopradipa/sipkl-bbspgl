<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LowonganStoreRequest;
use App\Http\Requests\Admin\LowonganUpdateRequest;
use App\Models\Lowongan;
use Illuminate\Http\Request;

class LowonganController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('keyword');
        $lowongans = Lowongan::when($search, function ($query, $search) {
            return $query->where('posisi', 'like', "%{$search}%");
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
    public function store(LowonganStoreRequest $request)
    {
        $validated = $request->validated();
        if (!$validated['is_fulltime']) {
            $validated['hari_kerja'] = null;
        }
        Lowongan::create($validated);
        return redirect()->route('lowongan.index')
            ->with('success', 'Lowongan berhasil ditambahkan.');
    }
    public function show(Lowongan $lowongan)
    {
        return view('admin.lowongan.show', [
            'lowongan' => $lowongan,
            'title'    => 'Detail Lowongan',
            'header'   => 'Detail Lowongan: ' . $lowongan->posisi
        ]);
    }
    public function edit(Lowongan $lowongan)
    {
        return view('admin.lowongan.edit', [
            'lowongan' => $lowongan,
            'title'    => 'Edit Lowongan',
            'header'   => 'Edit Lowongan: ' . $lowongan->posisi
        ]);
    }
    public function update(LowonganUpdateRequest $request, Lowongan $lowongan)
    {
        $validated = $request->validated();
        if (!$validated['is_fulltime']) {
            $validated['hari_kerja'] = null;
        }
        $lowongan->update($validated);
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
