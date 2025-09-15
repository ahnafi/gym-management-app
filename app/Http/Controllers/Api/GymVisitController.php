<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GymVisit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class GymVisitController extends Controller
{
    /**
     * Get all gym visits (admin only)
     */
    public function index(Request $request)
    {
        // Check if user is admin
        if (!in_array($request->user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $query = GymVisit::with('user');

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('visit_date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('visit_date', '<=', $request->end_date);
        }

        // Search by user name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $visits = $query->orderBy('visit_date', 'desc')
                       ->orderBy('entry_time', 'desc')
                       ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $visits
        ]);
    }

    /**
     * Get user's own gym visits
     */
    public function myVisits(Request $request)
    {
        $user = $request->user();

        $query = GymVisit::where('user_id', $user->id);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('visit_date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('visit_date', '<=', $request->end_date);
        }

        $visits = $query->orderBy('visit_date', 'desc')
                       ->orderBy('entry_time', 'desc')
                       ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $visits
        ]);
    }

    /**
     * Get specific gym visit
     */
    public function show(Request $request, $id)
    {
        $visit = GymVisit::with('user')->findOrFail($id);

        // Check if user is admin or viewing their own visit
        if (!in_array($request->user()->role, ['admin', 'super_admin']) && 
            $request->user()->id !== $visit->user_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $visit
        ]);
    }

    /**
     * Check in to gym
     */
    public function checkIn(Request $request)
    {
        $user = $request->user();

        // Check if user has active membership
        if ($user->membership_status !== 'active' || $user->membership_end_date < now()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Active membership required to enter the gym'
            ], 400);
        }

        try {
            // Check if user is already checked in today
            $existingVisit = GymVisit::where('user_id', $user->id)
                                   ->where('visit_date', now()->toDateString())
                                   ->where('status', 'checked_in')
                                   ->first();

            if ($existingVisit) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are already checked in today',
                    'data' => $existingVisit
                ], 400);
            }

            $visit = GymVisit::create([
                'user_id' => $user->id,
                'visit_date' => now()->toDateString(),
                'entry_time' => now(),
                'status' => 'checked_in'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Checked in successfully',
                'data' => $visit
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Check-in failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check out from gym
     */
    public function checkOut(Request $request)
    {
        $user = $request->user();

        try {
            // Find today's check-in record
            $visit = GymVisit::where('user_id', $user->id)
                            ->where('visit_date', now()->toDateString())
                            ->where('status', 'checked_in')
                            ->first();

            if (!$visit) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No active check-in found for today'
                ], 400);
            }

            $visit->update([
                'exit_time' => now(),
                'status' => 'completed'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Checked out successfully',
                'data' => $visit
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Check-out failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current visit status
     */
    public function currentStatus(Request $request)
    {
        $user = $request->user();

        $currentVisit = GymVisit::where('user_id', $user->id)
                              ->where('visit_date', now()->toDateString())
                              ->orderBy('entry_time', 'desc')
                              ->first();

        if (!$currentVisit) {
            return response()->json([
                'status' => 'success',
                'message' => 'No visit record for today',
                'data' => [
                    'is_checked_in' => false,
                    'visit' => null
                ]
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'is_checked_in' => $currentVisit->status === 'checked_in',
                'visit' => $currentVisit
            ]
        ]);
    }

    /**
     * Manual check-in/out by admin
     */
    public function manualEntry(Request $request)
    {
        // Check if user is admin
        if (!in_array($request->user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'visit_date' => 'required|date',
            'entry_time' => 'required|date',
            'exit_time' => 'nullable|date|after:entry_time',
            'status' => 'required|in:checked_in,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::findOrFail($request->user_id);

            // Check if user has/had active membership on visit date
            $visitDate = Carbon::parse($request->visit_date);
            if ($user->membership_status !== 'active' && 
                ($user->membership_end_date < $visitDate || !$user->membership_end_date)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User did not have active membership on the specified date'
                ], 400);
            }

            $visitData = $request->only(['user_id', 'visit_date', 'entry_time', 'exit_time', 'status']);

            $visit = GymVisit::create($visitData);

            return response()->json([
                'status' => 'success',
                'message' => 'Manual entry created successfully',
                'data' => $visit->load('user')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Manual entry failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update gym visit (admin only)
     */
    public function update(Request $request, $id)
    {
        // Check if user is admin
        if (!in_array($request->user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $visit = GymVisit::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'visit_date' => 'sometimes|date',
            'entry_time' => 'sometimes|date',
            'exit_time' => 'nullable|date|after:entry_time',
            'status' => 'sometimes|in:checked_in,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = $request->only(['visit_date', 'entry_time', 'exit_time', 'status']);

            $visit->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Gym visit updated successfully',
                'data' => $visit->load('user')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Visit update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete gym visit (admin only)
     */
    public function destroy(Request $request, $id)
    {
        // Check if user is admin
        if (!in_array($request->user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        try {
            $visit = GymVisit::findOrFail($id);
            $visit->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Gym visit deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Visit deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get visit statistics
     */
    public function statistics(Request $request)
    {
        // Check if user is admin
        if (!in_array($request->user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $today = now()->toDateString();
        $thisWeek = now()->startOfWeek();
        $thisMonth = now()->startOfMonth();

        $stats = [
            'visits_today' => GymVisit::where('visit_date', $today)->count(),
            'active_visits_now' => GymVisit::where('visit_date', $today)
                                         ->where('status', 'checked_in')
                                         ->count(),
            'visits_this_week' => GymVisit::where('visit_date', '>=', $thisWeek->toDateString())->count(),
            'visits_this_month' => GymVisit::where('visit_date', '>=', $thisMonth->toDateString())->count(),
            'average_visit_duration' => $this->calculateAverageVisitDuration(),
            'busiest_hours' => $this->getBusiestHours(),
            'frequent_visitors' => User::withCount(['gymVisits' => function($query) use ($thisMonth) {
                                        $query->where('visit_date', '>=', $thisMonth->toDateString());
                                    }])
                                    ->having('gym_visits_count', '>', 0)
                                    ->orderBy('gym_visits_count', 'desc')
                                    ->take(10)
                                    ->get(['id', 'name', 'email']),
            'daily_visits_this_month' => GymVisit::selectRaw('visit_date, COUNT(*) as visits')
                                               ->where('visit_date', '>=', $thisMonth->toDateString())
                                               ->groupBy('visit_date')
                                               ->orderBy('visit_date', 'asc')
                                               ->get()
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }

    /**
     * Get user's visit statistics
     */
    public function myStatistics(Request $request)
    {
        $user = $request->user();
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        $stats = [
            'total_visits' => GymVisit::where('user_id', $user->id)->count(),
            'visits_this_month' => GymVisit::where('user_id', $user->id)
                                         ->where('visit_date', '>=', $thisMonth->toDateString())
                                         ->count(),
            'visits_last_month' => GymVisit::where('user_id', $user->id)
                                         ->where('visit_date', '>=', $lastMonth->toDateString())
                                         ->where('visit_date', '<', $thisMonth->toDateString())
                                         ->count(),
            'average_visits_per_week' => $this->calculateUserAverageVisitsPerWeek($user->id),
            'longest_streak' => $this->calculateUserLongestStreak($user->id),
            'current_streak' => $this->calculateUserCurrentStreak($user->id),
            'favorite_visit_time' => $this->getUserFavoriteVisitTime($user->id),
            'recent_visits' => GymVisit::where('user_id', $user->id)
                                     ->orderBy('visit_date', 'desc')
                                     ->orderBy('entry_time', 'desc')
                                     ->take(10)
                                     ->get()
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }

    /**
     * Calculate average visit duration
     */
    private function calculateAverageVisitDuration()
    {
        $completedVisits = GymVisit::where('status', 'completed')
                                 ->whereNotNull('exit_time')
                                 ->get();

        if ($completedVisits->isEmpty()) {
            return 0;
        }

        $totalMinutes = 0;
        foreach ($completedVisits as $visit) {
            $duration = Carbon::parse($visit->exit_time)->diffInMinutes(Carbon::parse($visit->entry_time));
            $totalMinutes += $duration;
        }

        return round($totalMinutes / $completedVisits->count());
    }

    /**
     * Get busiest hours
     */
    private function getBusiestHours()
    {
        return GymVisit::selectRaw('HOUR(entry_time) as hour, COUNT(*) as visits')
                      ->groupBy('hour')
                      ->orderBy('visits', 'desc')
                      ->take(5)
                      ->get()
                      ->map(function($item) {
                          return [
                              'hour' => $item->hour . ':00',
                              'visits' => $item->visits
                          ];
                      });
    }

    /**
     * Calculate user's average visits per week
     */
    private function calculateUserAverageVisitsPerWeek($userId)
    {
        $firstVisit = GymVisit::where('user_id', $userId)->orderBy('visit_date', 'asc')->first();
        
        if (!$firstVisit) {
            return 0;
        }

        $weeks = Carbon::parse($firstVisit->visit_date)->diffInWeeks(now()) + 1;
        $totalVisits = GymVisit::where('user_id', $userId)->count();

        return round($totalVisits / $weeks, 1);
    }

    /**
     * Calculate user's longest streak
     */
    private function calculateUserLongestStreak($userId)
    {
        $visits = GymVisit::where('user_id', $userId)
                         ->orderBy('visit_date', 'asc')
                         ->pluck('visit_date')
                         ->unique()
                         ->values();

        if ($visits->isEmpty()) {
            return 0;
        }

        $longestStreak = 1;
        $currentStreak = 1;

        for ($i = 1; $i < $visits->count(); $i++) {
            $prevDate = Carbon::parse($visits[$i - 1]);
            $currentDate = Carbon::parse($visits[$i]);

            if ($currentDate->diffInDays($prevDate) === 1) {
                $currentStreak++;
                $longestStreak = max($longestStreak, $currentStreak);
            } else {
                $currentStreak = 1;
            }
        }

        return $longestStreak;
    }

    /**
     * Calculate user's current streak
     */
    private function calculateUserCurrentStreak($userId)
    {
        $visits = GymVisit::where('user_id', $userId)
                         ->orderBy('visit_date', 'desc')
                         ->pluck('visit_date')
                         ->unique()
                         ->values();

        if ($visits->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $checkDate = now()->toDateString();

        foreach ($visits as $visitDate) {
            if ($visitDate === $checkDate) {
                $streak++;
                $checkDate = Carbon::parse($checkDate)->subDay()->toDateString();
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Get user's favorite visit time
     */
    private function getUserFavoriteVisitTime($userId)
    {
        $favoriteHour = GymVisit::where('user_id', $userId)
                              ->selectRaw('HOUR(entry_time) as hour, COUNT(*) as count')
                              ->groupBy('hour')
                              ->orderBy('count', 'desc')
                              ->first();

        return $favoriteHour ? $favoriteHour->hour . ':00' : null;
    }
}