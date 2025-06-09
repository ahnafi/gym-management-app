<?php

namespace App\Http\Controllers;

use App\Models\GymClassAttendance;
use App\Models\MembershipHistory;
use App\Models\PersonalTrainer;
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
    public function gymClassHistory()
    {
        $gymClassHistories = GymClassAttendance::query()
            ->where('user_id', auth()->id())
            ->with([
                'gymClassSchedule:id,date,start_time,end_time,gym_class_id',
                'gymClassSchedule.gymClass:id,code,name,images'
            ])
            ->orderBy('created_at', 'desc')
            ->get(['id', 'user_id', 'status', 'gym_class_schedule_id', 'created_at']);

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
                'personalTrainerSchedules' => function ($query) {
                    $query->select('id', 'scheduled_at', 'check_in_time', 'check_out_time', 'personal_trainer_assignment_id');
                },
                'personalTrainerPackage:id,code,name,day_duration,images,personal_trainer_id',
                'personalTrainerPackage.personalTrainer:id,code,nickname,images'
            ])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        $personalTrainers = PersonalTrainer::select(['id', 'nickname'])->get();
        $personalTrainerPackages = PersonalTrainerPackage::select(['id', 'name'])->get();

        return Inertia::render("history/personalTrainingHistory/index", [
            'personalTrainingHistories' => $personalTrainingHistories,
            'personalTrainers' => $personalTrainers,
            'personalTrainerPackages' => $personalTrainerPackages,
        ]);
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
