<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GymClass;
use App\Models\GymClassSchedule;
use App\Models\GymClassAttendance;
use App\Services\AssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class GymClassController extends Controller
{
    /**
     * Get all gym classes
     */
    public function index(Request $request)
    {
        $query = GymClass::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            // Default to active classes for public access
            $query->where('status', 'active');
        }

        // Search by name or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Sort by price or name
        if ($request->has('sort_by')) {
            $sortBy = $request->sort_by;
            $sortOrder = $request->get('sort_order', 'asc');
            
            if (in_array($sortBy, ['price', 'name', 'created_at'])) {
                $query->orderBy($sortBy, $sortOrder);
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        $classes = $query->with('gymClassSchedules')
                        ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $classes
        ]);
    }

    /**
     * Get specific gym class
     */
    public function show($id)
    {
        $class = GymClass::with(['gymClassSchedules' => function($query) {
            $query->where('date', '>=', now()->toDateString())
                  ->orderBy('date', 'asc')
                  ->orderBy('start_time', 'asc');
        }])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $class
        ]);
    }

    /**
     * Create gym class (admin only)
     */
    public function store(Request $request)
    {
        // Check if user is admin
        if (!in_array($request->user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $classData = $request->only(['name', 'description', 'price', 'status']);

            // Handle image uploads
            if ($request->hasFile('images')) {
                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('gym_classes', 'public');
                    $imagePaths[] = $path;
                }
                $classData['images'] = $imagePaths;
            }

            $gymClass = GymClass::create($classData);

            return response()->json([
                'status' => 'success',
                'message' => 'Gym class created successfully',
                'data' => $gymClass
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Class creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update gym class (admin only)
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

        $gymClass = GymClass::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:active,inactive',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = $request->only(['name', 'description', 'price', 'status']);

            // Handle image uploads
            if ($request->hasFile('images')) {
                // Delete old images
                if ($gymClass->images) {
                    foreach ($gymClass->images as $oldImage) {
                        Storage::disk('public')->delete($oldImage);
                    }
                }

                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('gym_classes', 'public');
                    $imagePaths[] = $path;
                }
                $updateData['images'] = $imagePaths;
            }

            $gymClass->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Gym class updated successfully',
                'data' => $gymClass
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Class update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete gym class (admin only)
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
            $gymClass = GymClass::findOrFail($id);
            
            // Check if class has scheduled sessions
            $hasSchedules = GymClassSchedule::where('gym_class_id', $id)
                                          ->where('date', '>=', now()->toDateString())
                                          ->exists();
            
            if ($hasSchedules) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete class as it has upcoming schedules'
                ], 400);
            }

            $gymClass->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Gym class deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Class deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get class schedules
     */
    public function schedules(Request $request, $classId)
    {
        $gymClass = GymClass::findOrFail($classId);

        $query = GymClassSchedule::where('gym_class_id', $classId);

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('date', '>=', $request->start_date);
        } else {
            // Default to future schedules
            $query->where('date', '>=', now()->toDateString());
        }

        if ($request->has('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        $schedules = $query->with(['gymClassAttendances.user'])
                          ->orderBy('date', 'asc')
                          ->orderBy('start_time', 'asc')
                          ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => [
                'class' => $gymClass,
                'schedules' => $schedules
            ]
        ]);
    }

    /**
     * Create class schedule (admin only)
     */
    public function createSchedule(Request $request, $classId)
    {
        // Check if user is admin
        if (!in_array($request->user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $gymClass = GymClass::findOrFail($classId);

        $validator = Validator::make($request->all(), [
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot' => 'required|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check for conflicting schedules
            $conflict = GymClassSchedule::where('gym_class_id', $classId)
                                      ->where('date', $request->date)
                                      ->where(function($query) use ($request) {
                                          $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                                                ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                                                ->orWhere(function($q) use ($request) {
                                                    $q->where('start_time', '<=', $request->start_time)
                                                      ->where('end_time', '>=', $request->end_time);
                                                });
                                      })->exists();

            if ($conflict) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Schedule conflicts with existing session'
                ], 400);
            }

            $schedule = GymClassSchedule::create([
                'gym_class_id' => $classId,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'slot' => $request->slot,
                'available_slot' => $request->slot,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Schedule created successfully',
                'data' => $schedule
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Schedule creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update class schedule (admin only)
     */
    public function updateSchedule(Request $request, $classId, $scheduleId)
    {
        // Check if user is admin
        if (!in_array($request->user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $schedule = GymClassSchedule::where('gym_class_id', $classId)
                                  ->findOrFail($scheduleId);

        $validator = Validator::make($request->all(), [
            'date' => 'sometimes|date|after_or_equal:today',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'slot' => 'sometimes|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = $request->only(['date', 'start_time', 'end_time', 'slot']);

            // If slot is updated, adjust available_slot
            if ($request->has('slot')) {
                $attendanceCount = GymClassAttendance::where('gym_class_schedule_id', $scheduleId)->count();
                $updateData['available_slot'] = max(0, $request->slot - $attendanceCount);
            }

            $schedule->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Schedule updated successfully',
                'data' => $schedule
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Schedule update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete class schedule (admin only)
     */
    public function destroySchedule(Request $request, $classId, $scheduleId)
    {
        // Check if user is admin
        if (!in_array($request->user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        try {
            $schedule = GymClassSchedule::where('gym_class_id', $classId)
                                      ->findOrFail($scheduleId);

            // Check if there are attendees
            $hasAttendees = GymClassAttendance::where('gym_class_schedule_id', $scheduleId)->exists();
            
            if ($hasAttendees) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete schedule as it has registered attendees'
                ], 400);
            }

            $schedule->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Schedule deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Schedule deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Book class session
     */
    public function bookClass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gym_class_id' => 'required|exists:gym_classes,id',
            'gym_class_schedule_id' => 'required|exists:gym_class_schedules,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $gymClass = GymClass::findOrFail($request->gym_class_id);
            $schedule = GymClassSchedule::findOrFail($request->gym_class_schedule_id);

            // Check if class is active
            if ($gymClass->status !== 'active') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This class is not available'
                ], 400);
            }

            // Check if user has active membership
            if ($user->membership_status !== 'active' || $user->membership_end_date < now()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Active membership required to book classes'
                ], 400);
            }

            // Check if already booked
            $existingBooking = GymClassAttendance::where('user_id', $user->id)
                                                ->where('gym_class_schedule_id', $schedule->id)
                                                ->exists();

            if ($existingBooking) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have already booked this class session'
                ], 400);
            }

            // Use AssignmentService to book the class
            $attendance = AssignmentService::assignGymClass(
                $user->id,
                $gymClass->id,
                $schedule->id
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Class booked successfully',
                'data' => $attendance
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel class booking
     */
    public function cancelBooking(Request $request, $attendanceId)
    {
        try {
            $user = $request->user();
            $attendance = GymClassAttendance::where('user_id', $user->id)
                                          ->findOrFail($attendanceId);

            $schedule = $attendance->gymClassSchedule;

            // Check if cancellation is allowed (e.g., at least 24 hours before class)
            $classDateTime = \Carbon\Carbon::parse($schedule->date . ' ' . $schedule->start_time);
            if ($classDateTime->diffInHours(now()) < 24) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot cancel booking less than 24 hours before class'
                ], 400);
            }

            // Delete attendance and increment available slot
            DB::transaction(function() use ($attendance, $schedule) {
                $attendance->delete();
                $schedule->increment('available_slot');
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Booking cancelled successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cancellation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's class bookings
     */
    public function myBookings(Request $request)
    {
        $user = $request->user();

        $query = GymClassAttendance::where('user_id', $user->id);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereHas('gymClassSchedule', function($q) use ($request) {
                $q->whereDate('date', '>=', $request->start_date);
            });
        }

        if ($request->has('end_date')) {
            $query->whereHas('gymClassSchedule', function($q) use ($request) {
                $q->whereDate('date', '<=', $request->end_date);
            });
        }

        $bookings = $query->with(['gymClassSchedule.gymClass'])
                         ->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $bookings
        ]);
    }

    /**
     * Mark attendance (admin only)
     */
    public function markAttendance(Request $request, $attendanceId)
    {
        // Check if user is admin
        if (!in_array($request->user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:present,absent',
            'attended_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $attendance = GymClassAttendance::findOrFail($attendanceId);
            
            $updateData = ['status' => $request->status];
            
            if ($request->status === 'present') {
                $updateData['attended_at'] = $request->attended_at ?? now();
            }

            $attendance->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance marked successfully',
                'data' => $attendance
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Attendance marking failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get class statistics (admin only)
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

        $stats = [
            'total_classes' => GymClass::count(),
            'active_classes' => GymClass::where('status', 'active')->count(),
            'total_schedules_this_month' => GymClassSchedule::whereMonth('date', now()->month)
                                                           ->whereYear('date', now()->year)
                                                           ->count(),
            'total_bookings_this_month' => GymClassAttendance::whereMonth('created_at', now()->month)
                                                            ->whereYear('created_at', now()->year)
                                                            ->count(),
            'attendance_rate' => $this->calculateAttendanceRate(),
            'popular_classes' => GymClass::withCount('gymClassSchedules')
                                        ->orderBy('gym_class_schedules_count', 'desc')
                                        ->take(5)
                                        ->get(),
            'upcoming_schedules' => GymClassSchedule::where('date', '>=', now()->toDateString())
                                                  ->with(['gymClass', 'gymClassAttendances'])
                                                  ->orderBy('date', 'asc')
                                                  ->take(10)
                                                  ->get()
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }

    /**
     * Calculate attendance rate
     */
    private function calculateAttendanceRate()
    {
        $totalBookings = GymClassAttendance::count();
        $attendedBookings = GymClassAttendance::where('status', 'present')->count();

        return $totalBookings > 0 ? round(($attendedBookings / $totalBookings) * 100, 2) : 0;
    }
}