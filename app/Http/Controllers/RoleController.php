<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class RoleController extends Controller
{
    /**
     * Menampilkan daftar semua peran (roles).
     */
    public function index(Request $request)
    {
        $search = $request->keyword;
        $roles = Role::when($search, function ($query, $search) {
                return $query->where('nama_role', 'like', "%{$search}%");
            })
            ->latest('id_role')
            ->paginate(10)
            ->appends(['keyword' => $search]);

        return view('admin.role.index', [
            'roles' => $roles,
            'title' => 'Peran (Role)',
            'header' => 'Halaman Peran (Role)'
        ]);
    }

    /**
     * Menampilkan form untuk membuat peran (role) baru.
     */
    public function create()
    {
        return view('admin.role.create', [
            'title' => 'Tambah Peran Baru',
            'header' => 'Formulir Peran (Role) Baru'
        ]);
    }

    /**
     * Menyimpan peran (role) baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi: nama_role harus diisi dan unik (tidak boleh sama)
        $validatedData = $request->validate([
            'nama_role' => 'required|string|max:50|unique:roles,nama_role',
        ], [
            'nama_role.required' => 'Nama peran wajib diisi.',
            'nama_role.unique' => 'Nama peran ini sudah ada, gunakan nama lain.',
        ]);

        Role::create($validatedData);

        return redirect()->route('role.index')
            ->with('success', 'Peran (Role) baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit peran (role).
     * (Kita tidak menggunakan show() karena 'edit' sudah cukup untuk CRUD ini)
     */
    public function show(Role $role)
    {
        return view('admin.role.show', [
            'role' => $role,
            'title' => 'Detail Role',
            'header' => 'Detail Role'
        ]);
    }

    /**
     * Menampilkan form untuk mengedit peran (role).
     */
    public function edit(Role $role) // Menggunakan Route Model Binding
    {
        return view('admin.role.edit', [
            'role' => $role,
            'title' => 'Edit Peran (Role)',
            'header' => 'Form Edit Peran (Role)'
        ]);
    }

    /**
     * Memperbarui data peran (role) di database.
     */
    public function update(Request $request, Role $role)
    {
        // Validasi: nama_role harus unik, KECUALI untuk ID-nya sendiri
        $validatedData = $request->validate([
            'nama_role' => [
                'required',
                'string',
                'max:50',
                Rule::unique('roles', 'nama_role')->ignore($role->id_role, 'id_role')
            ],
        ], [
            'nama_role.required' => 'Nama peran wajib diisi.',
            'nama_role.unique' => 'Nama peran ini sudah ada, gunakan nama lain.',
        ]);

        $role->update($validatedData);

        return redirect()->route('role.index')
            ->with('success', 'Peran (Role) berhasil diperbarui.');
    }

    /**
     * Menghapus peran (role) dari database.
     */
    public function destroy(Role $role)
    {
        try {
            // Coba hapus
            $role->delete();
            
            return redirect()->route('role.index')
                ->with('success', 'Peran (Role) berhasil dihapus.');

        } catch (QueryException $e) {
            // Tangkap error jika gagal (karena foreign key constraint)
            // Kode error 1451 adalah error constraint (role masih dipakai user)
            if ($e->errorInfo[1] == 1451) { 
                return redirect()->route('role.index')
                    ->with('error', 'Gagal menghapus! Peran (Role) ini masih digunakan oleh pengguna.');
            }
            
            // Error lainnya
            return redirect()->route('role.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}