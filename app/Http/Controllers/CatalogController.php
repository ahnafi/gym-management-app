<?php

namespace App\Http\Controllers;

use App\Models\PersonalTrainer;
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

    public function membershipPackageDetail(MembershipPackage $membershipPackage)
    {
        $durationInMonths = round($membershipPackage->duration / 30, 1);

        return Inertia::render('membershipPackages/detail', [
            'membershipPackage' => [
                'id' => $membershipPackage->id,
                'code' => $membershipPackage->code,
                'name' => $membershipPackage->name,
                'slug' => $membershipPackage->slug,
                'description' => $membershipPackage->description,
                'duration' => $membershipPackage->duration,
                'duration_in_months' => $durationInMonths,
                'price' => $membershipPackage->price,
                'status' => $membershipPackage->status,
                'images' => $membershipPackage->images,
                'created_at' => $membershipPackage->created_at->toDateTimeString(),
                'updated_at' => $membershipPackage->updated_at->toDateTimeString(),
            ]
        ]);
    }

    public function gymClasses()
    {
        $gymClasses = GymClass::active()->get();

        return Inertia::render('gymClasses/index', compact('gymClasses'));
    }

    public function gymClassSchedule(GymClass $gymClass)
    {
        $gymClass->load('gymClassSchedules');

        return Inertia::render('gymClasses/schedule', [
            'gymClass' => [
                'id' => $gymClass->id,
                'code' => $gymClass->code,
                'name' => $gymClass->name,
                'slug' => $gymClass->slug,
                'description' => $gymClass->description,
                'price' => $gymClass->price,
                'images' => $gymClass->images ? json_decode($gymClass->images) : null,
                'status' => $gymClass->status,
                'created_at' => $gymClass->created_at->toDateTimeString(),
                'updated_at' => $gymClass->updated_at->toDateTimeString(),

                // map schedules explicitly
                'gymClassSchedules' => $gymClass->gymClassSchedules->map(function ($schedule) {
                    return [
                        'id' => $schedule->id,
                        'date' => $schedule->date->toDateString(),
                        'start_time' => $schedule->start_time->format('H:i:s'),
                        'end_time' => $schedule->end_time->format('H:i:s'),
                        'slot' => $schedule->slot,
                        'created_at' => $schedule->created_at->toDateTimeString(),
                        'updated_at' => $schedule->updated_at->toDateTimeString(),
                    ];
                }),
            ],
        ]);
    }

    public function personalTrainers() {
        $personalTrainers = PersonalTrainer::all();

        return Inertia::render('personalTrainer/index', compact('personalTrainers'));
    }

    public function trainerDetail(PersonalTrainer $personalTrainer)
    {
        $personalTrainer->load(['personalTrainerPackages' => function ($query) {
            $query->active();
        }]);

        return Inertia::render('personalTrainer/personalTrainerDetail', [
            'personalTrainer' => [
                'id' => $personalTrainer->id,
                'name' => $personalTrainer->name,
                'slug' => $personalTrainer->slug,
                'bio' => $personalTrainer->bio,
                'image' => $personalTrainer->image,
                'status' => $personalTrainer->status,
                'created_at' => $personalTrainer->created_at->toDateTimeString(),
                'updated_at' => $personalTrainer->updated_at->toDateTimeString(),
                'personalTrainerPackages' => $personalTrainer->personalTrainerPackages->map(function ($package) {
                    return [
                        'id' => $package->id,
                        'name' => $package->name,
                        'slug' => $package->slug,
                        'price' => $package->price,
                        'status' => $package->status,
                        'description' => $package->description,
                    ];
                }),
            ]
        ]);
    }
}
