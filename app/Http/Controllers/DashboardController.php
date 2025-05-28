<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $userData = auth()->user();
        $gymvisitsData = auth()->user()->gymVisits;
        $gymvisitsData =
        $gymMembershipsData = auth()->user()->membershipHistories;

        return Inertia::render('dashboard/index', [
            'gymVisitsData' => $gymvisitsData,
            'gymMembershipsData' => $gymMembershipsData,
            'userData' => $userData,
        ]);
    }
}
