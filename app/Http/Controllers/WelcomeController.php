<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Katalog;

class WelcomeController extends Controller
{
    public function index()
    {
        $katalogList = Katalog::with('inventori') // eager load relasi
            ->latest()
            ->take(6)
            ->get();

        return view('welcome', compact('katalogList'));
    }


    public function show($id)
    {
        $buku = Katalog::findOrFail($id);
        return view('detail-buku', compact('buku'));
    }
}
