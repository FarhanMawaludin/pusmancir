<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $activeMenu = 'dashboard';
        return view('admin.dashboard', compact('activeMenu'));
    }
}

