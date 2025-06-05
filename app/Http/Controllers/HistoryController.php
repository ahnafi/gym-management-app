<?php

namespace App\Http\Controllers;

use App\Models\GymClassAttendance;
use App\Models\MembershipHistory;
use App\Models\PersonalTrainerAssignment;
use App\Models\MembershipPackage;
use App\Models\GymClass;
use App\Models\PersonalTrainerPackage;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HistoryController extends Controller
{
    public function paymentHistory()
    {
        $paymentHistories = Transaction::query()
            ->where('user_id', auth()->id())
            ->with([
                'purchasable' => function (MorphTo $morphTo) {
                    $morphTo->morphWith([
                        MembershipPackage::class => ['id', 'code', 'name'],
                        GymClass::class => ['id', 'code', 'name'],
                        PersonalTrainerPackage::class => ['id', 'code', 'name'],
                    ]);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render("history/paymentHistory/index", compact('paymentHistories'));
    }

    public function gymClassHistory()
    {
        $gymClassHistories = GymClassAttendance::query()
            ->where('user_id', auth()->id())
            ->with([
                'gymClassSchedule:id,date,start_time,end_time,gym_class_id',
                'gymClassSchedule.gymClass:id,code,name,images'
            ])
            ->orderBy('created_at', 'desc')
            ->get(['id', 'user_id', 'gym_class_schedule_id', 'created_at']);

        $gymClasses = GymClass::select(['id', 'name'])->get();

        return Inertia::render("history/gymClassHistory/index", [
            'gymClassHistories' => $gymClassHistories,
            'gymClasses' => $gymClasses,
        ]);
    }

    public function personalTrainingHistory()
    {
        $personalTrainingHistories = PersonalTrainerAssignment::query()
            ->with([
                'PersonalTrainerSchedules:id,scheduled_at,check_in_time,check_out_time',
                'personalTrainerPackage:id,code,name,price,day_duration,images',
                'personalTrainerPackage.personalTrainer:id,name,images'
            ])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render("history/personalTrainingHistory/index", compact('personalTrainingHistories'));
    }

    public function membershipHistory()
    {
        $membershipHistories = MembershipHistory::query()
            ->where('user_id', auth()->id())
            ->with('membership_package')
            ->orderBy('created_at', 'desc')
            ->get();

        $membershipPackages = MembershipPackage::select(['id', 'name'])->get();

        return Inertia::render("history/membershipHistory/index", [
            'membershipHistories' => $membershipHistories,
            'membershipPackages' => $membershipPackages,
        ]);
    }
}
