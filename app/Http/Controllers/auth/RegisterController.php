<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\ProfilePeserta;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register', [
            'title' => 'Daftar Akun Baru'
        ]);
    }
    public function register(RegisterRequest $request)
    {
        $request->validated();
        try {
            DB::beginTransaction();
            $rolePeserta = Role::where('nama_role', 'peserta')->first();
            if (!$rolePeserta) {
                throw new \Exception('Role peserta belum disetting di database.');
            }
            $user = User::create(['name' => $request->name, 'email' => $request->email, 'password' => Hash::make($request->password), 'role_id' => $rolePeserta->id_role,]);
            $namaFoto = null;
            if ($request->hasFile('foto_profil')) {
                $file = $request->file('foto_profil');
                $namaFoto = $file->hashName();
                $file->storeAs('foto_profil', $namaFoto, 'public');
            }
            ProfilePeserta::create([
                'pengguna_id' => $user->id,
                'nim_nis'     => $request->nim_nis,
                'institusi'   => $request->institusi,
                'jurusan'     => $request->jurusan,
                'no_hp'       => $request->no_hp,
                'alamat'      => $request->alamat,
                'foto_profil' => $namaFoto,
            ]);
            DB::commit();
            Auth::login($user);
            return redirect()->intended(route('peserta.dashboard'))
                ->with('success', 'Pendaftaran berhasil! Selamat datang.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat mendaftar: ' . $e->getMessage())->withInput();
        }
    }
}
