<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardPustakawanController extends Controller
{
    public function index()
    {
        return view('pustakawan.dashboard');
    }
}

