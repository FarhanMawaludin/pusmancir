<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardAnggotaController extends Controller
{
    public function index()
    {
        $activeMenu = 'dashboard';
        return view('anggota.dashboard' , compact('activeMenu'));
    }
}

