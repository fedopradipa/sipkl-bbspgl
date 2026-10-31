<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PresensiIndexRequest;
use App\Http\Requests\Admin\PresensiStoreAdminRequest;
use App\Http\Requests\Admin\PresensiUpdateAdminRequest;
use App\Services\PresensiAdminService;
use Carbon\Carbon;

class PresensiAdminController extends Controller
{
    public function __construct(private PresensiAdminService $service) {}
    public function index(PresensiIndexRequest $request)
    {
        $request->validated();
        $tanggal      = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $lowonganId   = $request->input('lowongan_id');
        $statusFilter = $request->input('status');
        $keyword      = $request->input('keyword');
        $pesertaWajibHadir = $this->service->getPesertaWajibHadir($tanggal, $lowonganId, $keyword);
        $finalData = $this->service->buildFinalData($pesertaWajibHadir, $tanggal);
        $finalData = $this->service->filterByStatus($finalData, $statusFilter);
        $ringkasan = $this->service->buildRingkasan($finalData);
        $posisi_list = $this->service->getPosisiList();
        $pesertaAktif = $this->service->getPesertaAktifForDropdown($tanggal);

        return view('admin.presensi.index', [
            'presensis'        => $finalData,
            'ringkasan'        => $ringkasan,
            'tanggal'          => $tanggal,
            'posisi_list'      => $posisi_list,
            'selected_posisi'  => $lowonganId,
            'pesertaAktif'     => $pesertaAktif,
            'title'            => 'Presensi Harian Peserta'
        ]);
    }
    public function storeAdmin(PresensiStoreAdminRequest $request)
    {
        $validated = $request->validated();
        $this->service->storeAdmin($validated);
        return back()->with('success', 'Data presensi berhasil ditambahkan.');
    }
    public function updateAdmin(PresensiUpdateAdminRequest $request, $id)
    {
        $validated = $request->validated();
        $this->service->updateAdmin((int) $id, $validated);
        return back()->with('success', 'Data presensi berhasil diperbarui.');
    }
}
