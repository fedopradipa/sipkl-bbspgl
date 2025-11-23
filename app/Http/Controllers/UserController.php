<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role; // <-- DITAMBAHKAN
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // <-- DITAMBAHKAN
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash; // <-- DITAMBAHKAN

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna (users).
     */
    public function index(Request $request)
    {
        $search = $request->keyword;

        // Menggunakan eager loading 'role' (dari Model User)
        $users = User::with('role')
            ->when($search, function ($query, $search) {
                // Mencari berdasarkan nama atau email
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest('id') // 'id' adalah primary key default 'users'
            ->paginate(10)
            ->appends(['keyword' => $search]);

        return view('admin.user.index', [
            'users' => $users,
            'title' => 'Manajemen Pengguna',
            'header' => 'Daftar Pengguna (Admin)'
        ]);
    }

    /**
     * Menampilkan form untuk membuat pengguna (user) baru.
     */
    public function create()
    {
        // Ambil semua role untuk ditampilkan di dropdown
        $roles = Role::all();

        return view('admin.user.create', [
            'roles' => $roles, // <-- Kirim data roles
            'title' => 'Tambah Pengguna',
            'header' => 'Form Input Pengguna Baru'
        ]);
    }

    /**
     * Menyimpan pengguna (user) baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi input (Sesuai tabel users)
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed', // 'confirmed' akan mencari 'password_confirmation'
            'role_id' => 'required|exists:roles,id_role', // Pastikan role_id ada di tabel roles
            'nip' => 'nullable|string|max:50|unique:users,nip',
            'jabatan' => 'nullable|string|max:100',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role_id.required' => 'Peran (Role) wajib dipilih.',
            'role_id.exists' => 'Peran (Role) yang dipilih tidak valid.',
            'nip.unique' => 'NIP ini sudah terdaftar.',
        ]);

        // 2. Buat data baru (Hash password)
        // Kita tidak bisa pakai $validatedData langsung karena password
        $dataToCreate = $validatedData;
        $dataToCreate['password'] = Hash::make($request->password); // Enkripsi password

        User::create($dataToCreate);

        return redirect()->route('user.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail dari satu pengguna.
     * (Kita bisa skip 'show' dan langsung ke 'edit' seperti RoleController)
     */
    public function show(User $user)
    {
        // Load relasi role jika belum
        $user->load('role');

        return view('admin.user.show', [
            'user' => $user,
            'title' => 'Detail Pengguna',
            'header' => 'Detail Pengguna: ' . $user->name
        ]);
    }

    /**
     * Menampilkan form untuk mengedit pengguna.
     */
    public function edit(User $user) // Route Model Binding
    {
        // Ambil semua role untuk dropdown
        $roles = Role::all();

        return view('admin.user.edit', [
            'user' => $user,
            'roles' => $roles, // <-- Kirim data roles
            'title' => 'Edit Pengguna',
            'header' => 'Form Edit Pengguna: ' . $user->name
        ]);
    }

    /**
     * Memperbarui data pengguna di dalam database.
     */
    public function update(Request $request, User $user)
    {
        // 1. Validasi input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id, 'id') // Abaikan email user ini sendiri
            ],
            'role_id' => 'required|exists:roles,id_role',
            'nip' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('users', 'nip')->ignore($user->id, 'id') // Abaikan NIP user ini sendiri
            ],
            'jabatan' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role_id.required' => 'Peran (Role) wajib dipilih.',
        ]);

        // 2. Siapkan data untuk update
        $dataToUpdate = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'role_id' => $validatedData['role_id'],
            'nip' => $validatedData['nip'],
            'jabatan' => $validatedData['jabatan'],
        ];

        // 3. Cek apakah password diisi (opsional)
        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        // 4. Update data
        $user->update($dataToUpdate);

        return redirect()->route('user.index')->with('success', 'Data pengguna berhasil diupdate.');
    }

    /**
     * Menghapus pengguna dari database.
     */
    public function destroy(User $user)
    {
        // PENTING: Tambahkan perlindungan agar user tidak bisa menghapus diri sendiri
        if (auth()->id() == $user->id) {
            return redirect()->route('user.index')
                ->with('error', 'Gagal! Anda tidak dapat menghapus akun Anda sendiri.');
        }

        try {
            $user->delete();

            return redirect()->route('user.index')
                ->with('success', 'Pengguna berhasil dihapus.');
        } catch (QueryException $e) {
            // (Opsional) Tangkap error jika user terhubung ke data lain
            return redirect()->route('user.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
