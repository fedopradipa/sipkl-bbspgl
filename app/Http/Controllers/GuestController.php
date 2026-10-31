<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('keyword');
        $lowongans = Lowongan::tersedia()
            ->when($search, function ($query, $search) {
                return $query->where('posisi', 'like', "%{$search}%");
            })
            ->latest('window_start_date')
            ->paginate(9)
            ->appends(['keyword' => $search]);
        return view('guest.lowongan.index', [
            'lowongans' => $lowongans,
            'title' => 'Lowongan PKL Tersedia',
        ]);
    }

    public function show(Lowongan $lowongan)
    {
        if (!$lowongan->is_open) {
            abort(404);
        }
        return view('guest.lowongan.show', [
            'lowongan' => $lowongan,
            'title' => $lowongan->posisi
        ]);
    }
}
