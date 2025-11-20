<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard utama dengan ringkasan data.
     * --- DIPERBARUI (Sesuai Permintaan) ---
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.dashboard.index', [
             'title' => 'Dashboard',
             'header' => 'Dashboard Inventaris'
        ]);
    }
}