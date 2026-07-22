<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User;
use App\Models\Anggota;
use App\Models\Kelas;
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

        $query = User::with('anggota.kelas');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($category !== 'all') {
            $query->where('role', $category);
        }

        $query->orderByRaw("FIELD(role, 'admin', 'pustakawan', 'anggota')")
            ->orderBy('created_at', 'asc'); 

        $user = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category
        ]);

        $list_kelas = Kelas::orderBy('nama_kelas', 'asc')->get();

        return view('admin.pengguna.index', [
            'activeMenu' => $activeMenu,
            'user' => $user,
            'category' => $category,
            'search' => $search,
            'list_kelas' => $list_kelas,
        ]);
    }

    public function create()
    {
        $activeMenu = "pengguna";
        $list_kelas = Kelas::orderBy('nama_kelas', 'asc')->get();
        return view('admin.pengguna.create', [
            'activeMenu' => $activeMenu,
            'list_kelas' => $list_kelas,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'], // Asumsi username = NISN untuk anggota
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'pustakawan', 'anggota'])],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan sebelumnya.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Posisi wajib dipilih.',
            'role.in' => 'Posisi yang dipilih tidak valid.',
            'kelas_id.exists' => 'Kelas yang dipilih tidak valid.',
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

                // Gunakan username sebagai NISN dan QR code (karena diasumsikan unik)
                $nisn = $validated['username'];

                Anggota::create([
                    'user_id' => $user->id,
                    'no_telp' => null,
                    'email' => null,
                    'kelas_id' => $validated['kelas_id'] ?? null,
                    'nisn' => $nisn,
                    'qr_code' => $nisn, // Simpan NISN sebagai QR code
                ]);
            }

            return redirect()->route('admin.pengguna.index')
                ->with('success', 'Pengguna berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan pengguna. Pesan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = User::with('anggota.kelas')->findOrFail($id);
        $activeMenu = 'pengguna';
        $list_kelas = Kelas::orderBy('nama_kelas', 'asc')->get();
        return view('admin.pengguna.edit', [
            'activeMenu' => $activeMenu,
            'user' => $user,
            'list_kelas' => $list_kelas,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'pustakawan', 'anggota'])],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
            'username.required' => 'username wajib diisi.',
            'username.unique' => 'username sudah digunakan sebelumnya.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Posisi wajib dipilih.',
            'role.in' => 'Posisi yang dipilih tidak valid.',
            'kelas_id.exists' => 'Kelas yang dipilih tidak valid.',
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

            // Update kelas di data anggota jika role anggota
            if ($user->role === 'anggota' && $user->anggota) {
                $user->anggota->update(['kelas_id' => $validated['kelas_id'] ?? null]);
            }

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
            $user = User::findOrFail($id);

            // Hapus data anggota terkait terlebih dahulu
            if ($user->anggota) {
                $user->anggota->delete();
            }

            $user->delete();
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

        $jumlahBerhasil = 0;
        $jumlahDuplikat = 0;

        foreach (array_slice($rows, 1) as $row) {
            $name = $row[0] ?? null;
            $username = $row[1] ?? null;
            $password = $row[2] ?? null;
            $kelas_nama = $row[3] ?? null;

            // Cek jika ada data penting yang kosong
            if (!$name || !$username || !$password) {
                continue;
            }

            // Cek apakah username sudah ada
            if (User::where('username', $username)->exists()) {
                $jumlahDuplikat++;
                continue; // Lewati jika duplikat
            }

            // Cari kelas berdasarkan nama (jika ada)
            $kelas_id = null;
            if ($kelas_nama) {
                $kelas = Kelas::where('nama_kelas', trim($kelas_nama))->first();
                if ($kelas) {
                    $kelas_id = $kelas->id;
                }
            }

            // Buat user baru
            $user = User::create([
                'name' => $name,
                'username' => $username,
                'password' => Hash::make($password),
                'role' => 'anggota',
            ]);

            // Buat data anggota
            Anggota::create([
                'user_id' => $user->id,
                'nisn' => $username,
                'no_telp' => null,
                'email' => null,
                'kelas_id' => $kelas_id,
            ]);

            $jumlahBerhasil++;
        }

        // Kirim feedback ke user
        return redirect()->back()->with('success', "Import selesai. Berhasil ditambahkan: $jumlahBerhasil, Duplikat dilewati: $jumlahDuplikat.");
    }
}
