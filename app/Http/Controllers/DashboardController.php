<?php

namespace App\Http\Controllers;

use App\Models\MembershipPackage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\GymVisit;
use App\Models\MembershipHistory;
use App\Models\GymClassAttendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Whole User Data

        $gymVisits = GymVisit::query()
            ->where('user_id', $user->id)
            ->orderBy('visit_date', 'desc')
            ->get();

        $membershipHistories =  MembershipHistory::query()
            ->where('user_id', $user->id)
            ->with('membership_package')
            ->orderBy('start_date', 'desc')
            ->limit(10)
            ->get();

        $gymClassAttendances = GymClassAttendance::query()
            ->with([
                'gymClassSchedule:id,date,start_time,end_time,gym_class_id',
                'gymClassSchedule.gymClass:id,code,name'
            ])
            ->where('user_id', $user->id)
            ->orderBy('attended_at', 'desc')
            ->limit(10)
            ->get();

        // Summary Calculations

        $visitCountInCurrentMonth = $gymVisits->filter(function ($visit) {
            return Carbon::parse($visit->visit_date)->isCurrentMonth();
        })->count();

        $visitCountInCurrentWeek = $gymVisits->filter(function ($visit) {
            return Carbon::parse($visit->visit_date)->isCurrentWeek();
        })->count();


        $averageVisitTimeInCurrentMonth = $gymVisits
            ->filter(function ($visit) {
                return Carbon::parse($visit->visit_date)->isCurrentMonth() && $visit->exit_time !== null;
            })
            ->map(function ($visit) {
                $entry = Carbon::parse($visit->visit_date . ' ' . $visit->entry_time);
                $exit = Carbon::parse($visit->visit_date . ' ' . $visit->exit_time);

                return $entry->diffInMinutes($exit);
            })
            ->avg();

        $averageVisitTimeInCurrentMonth = round($averageVisitTimeInCurrentMonth, 2);

        $averageVisitTimeFormatted = $averageVisitTimeInCurrentMonth
            ? floor($averageVisitTimeInCurrentMonth / 60) . ' Jam ' . ($averageVisitTimeInCurrentMonth % 60) . ' Menit'
            : null;

        $currentMembership = $membershipHistories->first(function ($history) {
            return Carbon::now()->between($history->start_date, $history->end_date);
        });

        $currentMembershipPackageId = $currentMembership ? $currentMembership->membership_package_id : null;
        $currentMembershipPackage = MembershipPackage::find($currentMembershipPackageId);

        $gymClassCountInCurrentMonth = $gymClassAttendances->filter(function ($attendance) {
            return Carbon::parse($attendance->attended_at)->isCurrentMonth();
        })->count();

        return Inertia::render('dashboard/index', [
            'summary' => [
                'visitCountInCurrentMonth' => $visitCountInCurrentMonth,
                'visitCountInCurrentWeek' => $visitCountInCurrentWeek,
                'gymClassCountInCurrentMonth' => $gymClassCountInCurrentMonth,
                'averageVisitTimeInCurrentMonth' => $averageVisitTimeInCurrentMonth,
                'averageVisitTimeFormatted' => $averageVisitTimeFormatted,
                'currentMembership' => $currentMembership,
                'currentMembershipPackage' => $currentMembershipPackage
            ],
            'data' => [
                'user' => $user,
                'gymVisits' => $gymVisits,
                'membershipHistories' => $membershipHistories,
                'gymClassAttendances' => $gymClassAttendances,
            ]
        ]);
    }
}
