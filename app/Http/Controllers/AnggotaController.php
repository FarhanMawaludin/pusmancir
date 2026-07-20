<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;


class AnggotaController extends Controller
{

    public function index(Request $request)
    {
        $activeMenu = 'anggota';

        $search = $request->input('search');
        $category = $request->input('category', 'name');
        $sort_kelas = $request->input('sort_kelas');
        $filter_kelas = $request->input('filter_kelas');
        $limit = $request->input('limit', 10);

        $allowedCategories = ['name', 'nisn', 'kelas'];
        $category = in_array($category, $allowedCategories) ? $category : 'name';

        $query = User::select('users.*')
            ->with(['anggota.kelas'])
            ->where('users.role', 'anggota')
            ->whereHas('anggota', function ($q) {
                $q->where('status', 'aktif');
            });

        // Apply filter berdasarkan kelas jika dipilih
        if ($filter_kelas) {
            $query->whereHas('anggota', function ($q) use ($filter_kelas) {
                $q->where('kelas_id', $filter_kelas);
            });
        }

        // Apply sort berdasarkan kelas
        if ($sort_kelas === 'asc' || $sort_kelas === 'desc') {
            $query->join('anggota', 'anggota.user_id', '=', 'users.id')
                ->leftJoin('kelas', 'kelas.id', '=', 'anggota.kelas_id')
                ->orderBy('kelas.nama_kelas', $sort_kelas);
        }

        // Apply search filter
        $query->when($search, function ($q) use ($search, $category) {
            if ($category === 'name') {
                $q->where('users.name', 'like', "%{$search}%");
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

        // Determine per page limit
        if ($limit === 'all') {
            $count = (clone $query)->count();
            $perPage = $count > 0 ? $count : 10;
        } else {
            $perPage = (int)$limit;
            if (!in_array($perPage, [10, 50, 100])) {
                $perPage = 10;
            }
        }

        $users = $query->paginate($perPage)->appends([
            'search' => $search,
            'category' => $category,
            'sort_kelas' => $sort_kelas,
            'filter_kelas' => $filter_kelas,
            'limit' => $limit,
        ]);

        $list_kelas = Kelas::orderBy('nama_kelas', 'asc')->get();

        return view('admin.anggota.index', compact('users', 'search', 'category', 'sort_kelas', 'filter_kelas', 'list_kelas', 'limit', 'activeMenu'));
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
        $sort_kelas = $request->input('sort_kelas');
        $filter_kelas = $request->input('filter_kelas');
        $limit = $request->input('limit', 10);

        $allowedCategories = ['name', 'nisn', 'kelas'];
        $category = in_array($category, $allowedCategories) ? $category : 'name';

        $query = User::select('users.*')
            ->with(['anggota.kelas'])
            ->where('users.role', 'anggota')
            ->whereHas('anggota', function ($q) {
                $q->where('status', 'alumni');
            });

        // Apply filter berdasarkan kelas jika dipilih
        if ($filter_kelas) {
            $query->whereHas('anggota', function ($q) use ($filter_kelas) {
                $q->where('kelas_id', $filter_kelas);
            });
        }

        // Apply sort berdasarkan kelas
        if ($sort_kelas === 'asc' || $sort_kelas === 'desc') {
            $query->join('anggota', 'anggota.user_id', '=', 'users.id')
                ->leftJoin('kelas', 'kelas.id', '=', 'anggota.kelas_id')
                ->orderBy('kelas.nama_kelas', $sort_kelas);
        }

        // Apply search filter
        $query->when($search, function ($q) use ($search, $category) {
            if ($category === 'name') {
                $q->where('users.name', 'like', "%{$search}%");
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

        // Determine per page limit
        if ($limit === 'all') {
            $count = (clone $query)->count();
            $perPage = $count > 0 ? $count : 10;
        } else {
            $perPage = (int)$limit;
            if (!in_array($perPage, [10, 50, 100])) {
                $perPage = 10;
            }
        }

        $users = $query->paginate($perPage)->appends([
            'search' => $search,
            'category' => $category,
            'sort_kelas' => $sort_kelas,
            'filter_kelas' => $filter_kelas,
            'limit' => $limit,
        ]);

        $list_kelas = Kelas::orderBy('nama_kelas', 'asc')->get();

        return view('admin.anggota.indexAlumni', compact('users', 'search', 'category', 'sort_kelas', 'filter_kelas', 'list_kelas', 'limit', 'activeMenu'));
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
