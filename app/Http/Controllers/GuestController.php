<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('keyword');

        // BEST PRACTICE:
        // Gunakan scopeTersedia() agar user hanya melihat lowongan yang valid.
        $lowongans = Lowongan::tersedia()
            ->when($search, function ($query, $search) {
                return $query->where('judul', 'like', "%{$search}%")
                    ->orWhere('divisi', 'like', "%{$search}%");
            })
            ->latest('window_start_date') // Urutkan yang terbaru dibuka
            ->paginate(9) // Tampilkan 9 per halaman (Grid 3x3)
            ->appends(['keyword' => $search]);

        return view('guest.lowongan.index', [
            'lowongans' => $lowongans,
            'title' => 'Lowongan PKL Tersedia',
            // Tidak butuh $header karena layout tamu beda
        ]);
    }

    public function show(Lowongan $lowongan)
    {
        // Pastikan lowongan yang diakses detailnya juga masih tersedia
        // Jika user simpan link lama tapi lowongan sudah tutup, tolak aksesnya.
        if (!$lowongan->is_open) {
            abort(404);
        }

        return view('guest.lowongan.show', [
            'lowongan' => $lowongan,
            'title' => $lowongan->judul
        ]);
    }
}
