<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User;
use App\Models\Anggota;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Validator;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {

        $activeMenu = "pengguna";

        $search = $request->input('search');
        $category = $request->input('category', 'all');

        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($category !== 'all') {
            $query->where('role', $category);
        }

        $query->orderByRaw("FIELD(role, 'admin', 'pustakawan', 'anggota')");

        $user = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category
        ]);

        return view('admin.pengguna.index', [
            'activeMenu' => $activeMenu,
            'user' => $user,
            'category' => $category,
            'search' => $search,
        ]);
    }

    public function create()
    {
        $activeMenu = "pengguna";
        return view('admin.pengguna.create', [
            'activeMenu' => $activeMenu,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'pustakawan', 'anggota'])],
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan sebelumnya.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Posisi wajib dipilih.',
            'role.in' => 'Posisi yang dipilih tidak valid.',
        ]);

        try {
            // Simpan ke tabel users
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);

            // Jika role-nya "anggota", simpan ke tabel anggota
            if ($validated['role'] === 'anggota') {
                Anggota::create([
                    'user_id' => $user->id,
                    'no_telp' => null,
                    'email' => null,
                    'kelas_id' => null,
                    'nisn' => $validated['username'], // anggap field 'nisn' tersedia
                ]);
            }

            return redirect()->route('admin.pengguna.index')
                ->with('success', 'Pengguna berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan pengguna.');
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $activeMenu = 'pengguna';
        return view('admin.pengguna.edit', [
            'activeMenu' => $activeMenu,
            'user' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'pustakawan', 'anggota'])],
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
            'username.required' => 'username wajib diisi.',
            'username.unique' => 'username sudah digunakan sebelumnya.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Posisi wajib dipilih.',
            'role.in' => 'Posisi yang dipilih tidak valid.',
        ]);

        try {
            $updateData = [
                'name' => $validated['name'],
                'username' => $validated['username'],
                'role' => $validated['role'],
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user = User::findOrFail($id);
            $user->update($updateData);

            return redirect()->route('admin.pengguna.index')
                ->with('success', 'Pengguna berhasil diupdate.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Terjadi kesalahan saat mengupdate pengguna: ' . $e->getMessage()
                ]);
        }
    }

    public function destroy($id)
    {
        try {
            User::findOrFail($id)->delete();
            return redirect()->route('admin.pengguna.index')
                ->with('success', 'Pengguna berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus pengguna.');
        }
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $activeMenu = 'pengguna';
        return view('admin.pengguna.show', [
            'activeMenu' => $activeMenu,
            'user' => $user
        ]);
    }


    public function import(Request $request)
    {
        $request->validate([
            'file_pengguna' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file_pengguna');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        foreach (array_slice($rows, 1) as $row) {
            $name = $row[0] ?? null;
            $username = $row[1] ?? null;
            $password = $row[2] ?? null;

            // Lewati jika ada data penting yang kosong
            if (!$name || !$username || !$password) {
                continue;
            }

            // Lewati jika username sudah ada
            if (User::where('username', $username)->exists()) {
                continue;
            }

            // Buat user dengan role tetap = anggota
            $user = User::create([
                'name' => $name,
                'username' => $username,
                'password' => Hash::make($password),
                'role' => 'anggota',
            ]);

            // Tambahkan ke tabel anggota
            Anggota::create([
                'user_id' => $user->id,
                'nisn' => $username,
                'no_telp' => null,
                'email' => null,
                'kelas_id' => null,
            ]);
        }

        return redirect()->back()->with('success', 'Data anggota berhasil diimpor.');
    }
}
