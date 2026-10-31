<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

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
            'roles' => $roles,
            'title' => 'Tambah Pengguna',
            'header' => 'Form Input Pengguna Baru'
        ]);
    }

    /**
     * Menyimpan pengguna (user) baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi input (NIP dan Jabatan DIHAPUS sesuai view)
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed', // 'confirmed' akan mencari 'password_confirmation'
            'role_id' => 'required|exists:roles,id_role', // Pastikan role_id ada di tabel roles
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role_id.required' => 'Peran (Role) wajib dipilih.',
            'role_id.exists' => 'Peran (Role) yang dipilih tidak valid.',
        ]);

        // 2. Buat data baru (Hash password)
        $dataToCreate = $validatedData;
        $dataToCreate['password'] = Hash::make($request->password); // Enkripsi password

        User::create($dataToCreate);

        return redirect()->route('user.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail dari satu pengguna.
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
            'roles' => $roles,
            'title' => 'Edit Pengguna',
            'header' => 'Form Edit Pengguna: ' . $user->name
        ]);
    }

    /**
     * Memperbarui data pengguna di dalam database.
     */
    public function update(Request $request, User $user)
    {
        // 1. Validasi input (NIP dan Jabatan DIHAPUS)
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
            'password' => 'nullable|string|min:8|confirmed', // Password nullable saat edit
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
        ];

        // 3. Cek apakah password diisi (opsional saat edit)
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
            // Tangkap error jika user terhubung ke data lain (Foreign Key constraint)
            return redirect()->route('user.index')
                ->with('error', 'Gagal menghapus data: Data pengguna ini masih digunakan di tabel lain.');
        }
    }
}
