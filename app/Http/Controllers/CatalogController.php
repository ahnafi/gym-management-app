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
    public function membershipPackages()
    {
        $membershipPackages = MembershipPackage::active()
            ->get()
            ->map(function ($package) {
                $package->duration_in_months = round($package->duration / 30, 1);
                return $package;
            });


        return Inertia::render('membershipPackages/index', compact('membershipPackages'));
    }

    public function gymClasses()
    {
        $gymClasses = GymClass::active()->get();

        return Inertia::render('gymClasses/index', compact('gymClasses'));
    }

        public function trainerPackages()
        {
            $trainerPackages = PersonalTrainerPackage::active()->with('personalTrainer')->get();

            return Inertia::render('personalTrainerPackages/index', compact('trainerPackages'));
        }

        public function gymClassSchedule()
        {


            return Inertia::render('gymClasses/gymClassSchedule');
        }

    public function personalTrainers() {
        return Inertia::render('personalTrainer/index');
    }

}
