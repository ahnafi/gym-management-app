<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PersonalTrainerAssignment;
use App\Models\PersonalTrainer;
use App\Models\PersonalTrainerPackage;
use App\Models\PersonalTrainerSchedule;
use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;

class PersonalTrainerController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $personalTrainer = PersonalTrainer::where('user_personal_trainer_id', $user->id)->first();

        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // Schedule summaries
        $currentMonthSchedulesCount = PersonalTrainerSchedule::forTrainer($user->id)
            ->whereMonth('scheduled_at', $currentMonth)
            ->whereYear('scheduled_at', $currentYear)
            ->count();

        $currentWeekSchedulesCount = PersonalTrainerSchedule::forTrainer($user->id)
            ->whereBetween('scheduled_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])
            ->count();

        $totalSchedules = PersonalTrainerSchedule::forTrainer($user->id)->count();
        $completedSessions = PersonalTrainerSchedule::forTrainer($user->id)->where('status', 'completed')->count();
        $cancelledSessions = PersonalTrainerSchedule::forTrainer($user->id)->where('status', 'cancelled')->count();
        $missedSessions = PersonalTrainerSchedule::forTrainer($user->id)->where('status', 'missed')->count();

        // Assignments
        $totalAssignedClients = PersonalTrainerAssignment::where('personal_trainer_id', $user->id)->count();
        $completedAssignments = PersonalTrainerAssignment::where('personal_trainer_id', $user->id)->where('status', 'completed')->count();
        $inProgressAssignments = PersonalTrainerAssignment::where('personal_trainer_id', $user->id)->where('status', 'active')->count();

        // Trainee list with package
        $memberTrainee = PersonalTrainerAssignment::where('personal_trainer_id', $user->id)
            ->with('personalTrainerPackage', 'user')
            ->get();

        // Package Summary: Count how many times each package is taken
        $packageCounts = PersonalTrainerAssignment::where('personal_trainer_id', $user->id)
            ->with('personalTrainerPackage')
            ->get()
            ->groupBy('personalTrainerPackage.name')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'package' => $group->first()->personalTrainerPackage,
                ];
            })
            ->sortByDesc('count')
            ->values();

        $mostTakenPackage = $packageCounts->first();

        return Inertia::render('personalTrainerDashboard/index', [
            'summary' => [
                'currentMonthSchedulesCount' => $currentMonthSchedulesCount,
                'currentWeekSchedulesCount' => $currentWeekSchedulesCount,
                'totalSchedules' => $totalSchedules,
                'completedSessions' => $completedSessions,
                'cancelledSessions' => $cancelledSessions,
                'missedSessions' => $missedSessions,
                'totalAssignedClients' => $totalAssignedClients,
                'inProgressAssignments' => $inProgressAssignments,
                'completedAssignments' => $completedAssignments,
            ],
            'memberTrainee' => $memberTrainee,
            'packageSummary' => [
                'mostTakenPackage' => $mostTakenPackage,
                'allPackageCounts' => $packageCounts,
            ]
        ]);
    }
}
