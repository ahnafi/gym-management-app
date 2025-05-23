<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }

    public function show()
    {
        return view('dashboard.show');
    }

    public function edit()
    {
        return view('dashboard.edit');
    }
}
