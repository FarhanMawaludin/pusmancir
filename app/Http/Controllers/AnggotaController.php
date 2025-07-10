<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AnggotaController extends Controller
{

    public function index(Request $request)
    {
        $activeMenu = 'anggota';

        $search = $request->input('search');
        $category = $request->input('category', 'name'); // default 'name'

        $query = User::with(['anggota.kelas'])
            ->where('role', 'anggota');

        if ($search) {
            if ($category === 'name') {
                $query->where('name', 'like', "%{$search}%");
            } elseif ($category === 'nisn') {
                $query->whereHas('anggota', function ($q) use ($search) {
                    $q->where('nisn', 'like', "%{$search}%");
                });
            } elseif ($category === 'kelas') {
                $query->whereHas('anggota.kelas', function ($q) use ($search) {
                    $q->where('nama_kelas', 'like', "%{$search}%");
                });
            }
        }

        $users = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category,
        ]);

        return view('admin.anggota.index', compact('users', 'search', 'category', 'activeMenu'));
    }
}
