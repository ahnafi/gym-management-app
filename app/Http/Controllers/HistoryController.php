<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class HistoryController extends Controller
{
    public function paymentHistory()
    {
        return Inertia::render("history/paymentHistory");
    }

    public function gymClassHistory()
    {
        return Inertia::render("history/gymClassHistory");
    }

    public function personalTrainingHistory()
    {
        return Inertia::render("history/personalTrainingHistory");
    }

    public function membershipHistory()
    {
        return Inertia::render("history/membershipHistory");
    }
}
