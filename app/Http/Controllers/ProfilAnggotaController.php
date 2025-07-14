<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Anggota;
use App\Models\Kelas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

class ProfilAnggotaController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $anggota = User::with('anggota')->where('id', $user->id)->first();

        $activeMenu = 'profil';
        return view('anggota.profil.index', compact('activeMenu', 'anggota', 'user'));
    }

    public function edit()
    {
        $user = Auth::user();
        $anggota = Anggota::all();
        $kelas = Kelas::all();
        return view('anggota.profil.edit', [
            'user' => $user,
            'anggota' => $anggota,
            'kelas' => $kelas,
            'activeMenu' => 'profil',
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'       => 'nullable|string|max:255',
            'email'      => 'required|email|max:255',
            'nisn'       => 'required',
            'no_telp'    => ['required', 'string', 'max:20', 'regex:/^\d+$/'],
            'foto'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kelas_id'   => 'required|exists:kelas,id',
            'password'   => 'nullable|string|min:8|confirmed',
        ], [
            'name.required'       => 'Nama wajib diisi',
            'name.max'            => 'Nama tidak boleh lebih dari 255 karakter',
            'email.required'      => 'Email wajib diisi',
            'email.max'           => 'Email tidak boleh lebih dari 255 karakter',
            'email.email'         => 'Format email tidak valid',
            'nisn.required'       => 'NISN wajib diisi',
            'no_telp.required'    => 'No Telp wajib diisi',
            'no_telp.max'         => 'No Telp tidak boleh lebih dari 20 karakter',
            'no_telp.regex'       => 'No Telp harus berupa angka',
            'foto.image'          => 'Foto harus berupa gambar',
            'foto.max'            => 'Ukuran foto tidak boleh lebih dari 2MB',
            'foto.mimes'          => 'Format foto harus jpeg, png, jpg, atau gif',
            'kelas_id.required'   => 'Kelas wajib dipilih',
            'kelas_id.exists'     => 'Kelas tidak ditemukan',
            'password.min'        => 'Password minimal 8 karakter',
            'password.confirmed'  => 'Konfirmasi password tidak cocok',
        ]);

        try {
            // Ambil user dan relasi anggota-nya
            $user = User::with('anggota')->findOrFail($id);

            // Update tabel `users`
            $user->name = $validated['name'];
            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('foto_profil', 'public');
                $user->foto = $path;
            }
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();

            // Update tabel `anggota`
            if ($user->anggota) {
                $anggota = $user->anggota;
                $anggota->email     = $validated['email']; // ← Email disimpan di anggota
                $anggota->nisn      = $validated['nisn'];
                $anggota->no_telp   = $validated['no_telp'];
                $anggota->kelas_id  = $validated['kelas_id'];
                $anggota->save();
            }

            return redirect()->route('anggota.profil.index')
                ->with('success', 'Profil berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Terjadi kesalahan saat mengupdate profil: ' . $e->getMessage()
                ]);
        }
    }
}
