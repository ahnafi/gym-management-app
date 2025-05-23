<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GymClass;
use App\Models\PersonalTrainerPackage;
use App\Models\MembershipPackage;
use Inertia\Inertia;
use function Termwind\render;

class CatalogController extends Controller
{
    public function gymClasses()
    {
        $gymClasses = GymClass::all();

        return Inertia::render('gymClasses/index', compact('gymClasses'));
    }

    public function gymClassSchedule()
    {

    }

    public function personalTrainers() {
        return Inertia::render('personalTrainer/index');
    }

    public function trainerPackages()
    {
        $trainerPackages = PersonalTrainerPackage::all();

        return Inertia::render('personalTrainerPackages/index', compact('trainerPackages'));
    }

    public function membershipPackages()
    {
        $membershipPackages = MembershipPackage::all();

        return Inertia::render('membershipPackages/index', compact('membershipPackages'));
    }

}
