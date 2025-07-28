<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class AnggotaController extends Controller
{

    public function index(Request $request)
    {
        $activeMenu = 'anggota';

        $search = $request->input('search');
        $category = $request->input('category', 'name');

        $allowedCategories = ['name', 'nisn', 'kelas'];
        $category = in_array($category, $allowedCategories) ? $category : 'name';

        $query = User::with(['anggota.kelas'])
            ->where('role', 'anggota')
            ->whereHas('anggota', function ($q) {
                $q->where('status', 'aktif');
            })
            ->when($search, function ($q) use ($search, $category) {
                if ($category === 'name') {
                    $q->where('name', 'like', "%{$search}%");
                } elseif ($category === 'nisn') {
                    $q->whereHas('anggota', function ($q2) use ($search) {
                        $q2->where('nisn', 'like', "%{$search}%")
                            ->where('status', 'aktif'); // Pastikan tetap filter aktif di pencarian
                    });
                } elseif ($category === 'kelas') {
                    $q->whereHas('anggota.kelas', function ($q2) use ($search) {
                        $q2->where('nama_kelas', 'like', "%{$search}%");
                    })->whereHas('anggota', function ($q3) {
                        $q3->where('status', 'aktif');
                    });
                }
            });

        $users = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category,
        ]);

        return view('admin.anggota.index', compact('users', 'search', 'category', 'activeMenu'));
    }



    public function setAlumni(Request $request)
    {
        $ids = $request->anggota_ids;

        if (!$ids || count($ids) === 0) {
            return redirect()->back()->with('error', 'Tidak ada anggota yang dipilih.');
        }

        DB::table('anggota')
            ->whereIn('id', $ids)
            ->update(['status' => 'alumni']);

        return redirect()->back()->with('success', 'Status anggota berhasil diubah menjadi alumni.');
    }


    public function indexAlumni(Request $request)
    {
        $activeMenu = 'anggota';

        $search = $request->input('search');
        $category = $request->input('category', 'name');

        $allowedCategories = ['name', 'nisn', 'kelas'];
        $category = in_array($category, $allowedCategories) ? $category : 'name';

        $query = User::with(['anggota.kelas'])
            ->where('role', 'anggota')
            ->whereHas('anggota', function ($q) {
                $q->where('status', 'alumni');
            })
            ->when($search, function ($q) use ($search, $category) {
                if ($category === 'name') {
                    $q->where('name', 'like', "%{$search}%");
                } elseif ($category === 'nisn') {
                    $q->whereHas('anggota', function ($q2) use ($search) {
                        $q2->where('nisn', 'like', "%{$search}%")
                            ->where('status', 'alumni'); // Pastikan tetap filter aktif di pencarian
                    });
                } elseif ($category === 'kelas') {
                    $q->whereHas('anggota.kelas', function ($q2) use ($search) {
                        $q2->where('nama_kelas', 'like', "%{$search}%");
                    })->whereHas('anggota', function ($q3) {
                        $q3->where('status', 'alumni');
                    });
                }
            });

        $users = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category,
        ]);

        return view('admin.anggota.indexAlumni', compact('users', 'search', 'category', 'activeMenu'));
    }

    public function setAktif(Request $request)
    {
        $ids = $request->anggota_ids;

        if (!$ids || count($ids) === 0) {
            return redirect()->back()->with('error', 'Tidak ada anggota yang dipilih.');
        }

        DB::table('anggota')
            ->whereIn('id', $ids)
            ->update(['status' => 'aktif']);

        return redirect()->back()->with('success', 'Status anggota berhasil diubah menjadi aktif.');
    }

    public function show($id)
    {
        $activeMenu = 'anggota';

        $user = User::with(['anggota.kelas'])->where('role', 'anggota')->findOrFail($id);

        if (!$user->anggota) {
            return redirect()->route('admin.anggota.index')->with('error', 'Data anggota tidak ditemukan.');
        }

        return view('admin.anggota.show', compact('user', 'activeMenu'));
    }
}
