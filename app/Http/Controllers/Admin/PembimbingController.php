<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PembimbingStoreRequest;
use App\Http\Requests\Admin\PembimbingUpdateRequest;
use App\Models\Pembimbing;
use Illuminate\Http\Request;

class PembimbingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('keyword');
        $perPage = 10;
        $pembimbings = Pembimbing::when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('jabatan', 'like', "%{$search}%");
            });
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
    public function create()
    {
        return view('admin.pembimbing.create', [
            'title'  => 'Tambah Pembimbing',
            'header' => 'Tambah Pembimbing Baru'
        ]);
    }
    public function store(PembimbingStoreRequest $request)
    {
        $validatedData = $request->validated();
        Pembimbing::create($validatedData);
        return redirect()->route('pembimbing.index')
            ->with('success', 'Pembimbing berhasil ditambahkan.');
    }
    public function show(Pembimbing $pembimbing) {}
    public function edit(Pembimbing $pembimbing)
    {
        return view('admin.pembimbing.edit', [
            'pembimbing' => $pembimbing,
            'title'      => 'Edit Pembimbing',
            'header'     => 'Edit Pembimbing: ' . $pembimbing->nama
        ]);
    }
    public function update(PembimbingUpdateRequest $request, Pembimbing $pembimbing)
    {
        $validatedData = $request->validated();
        $pembimbing->update($validatedData);
        return redirect()->route('pembimbing.index')
            ->with('success', 'Data pembimbing berhasil diperbarui.');
    }
    public function destroy(Pembimbing $pembimbing)
    {
        $pembimbing->delete();
        return redirect()->route('pembimbing.index')
            ->with('success', 'Data pembimbing berhasil dihapus permanen.');
    }
}
