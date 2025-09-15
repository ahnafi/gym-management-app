<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PersonalTrainer;
use App\Models\PersonalTrainerPackage;
use App\Models\PersonalTrainerAssignment;
use App\Models\PersonalTrainerSchedule;
use App\Services\AssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PersonalTrainerController extends Controller
{
    /**
     * Get all personal trainers
     */
    public function index(Request $request)
    {
        $query = PersonalTrainer::with(['userPersonalTrainer', 'personalTrainerPackage']);

        // Search by nickname or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nickname', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('userPersonalTrainer', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $trainers = $query->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $trainers
        ]);
    }

    /**
     * Get specific personal trainer
     */
    public function show($id)
    {
        $trainer = PersonalTrainer::with([
            'userPersonalTrainer',
            'personalTrainerPackage' => function($query) {
                $query->where('status', 'active');
            }
        ])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $trainer
        ]);
    }

    /**
     * Create personal trainer profile (admin only)
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
            'user_personal_trainer_id' => 'required|exists:users,id',
            'nickname' => 'required|string|max:255',
            'description' => 'required|string',
            'metadata' => 'nullable|array',
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
            // Check if user is already a trainer
            $existingTrainer = PersonalTrainer::where('user_personal_trainer_id', $request->user_personal_trainer_id)->first();
            if ($existingTrainer) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User is already registered as a personal trainer'
                ], 400);
            }

            $trainerData = $request->only(['user_personal_trainer_id', 'nickname', 'description', 'metadata']);

            // Handle image uploads
            if ($request->hasFile('images')) {
                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('personal_trainers', 'public');
                    $imagePaths[] = $path;
                }
                $trainerData['images'] = $imagePaths;
            }

            $trainer = PersonalTrainer::create($trainerData);

            return response()->json([
                'status' => 'success',
                'message' => 'Personal trainer created successfully',
                'data' => $trainer->load('userPersonalTrainer')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Trainer creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update personal trainer profile
     */
    public function update(Request $request, $id)
    {
        $trainer = PersonalTrainer::findOrFail($id);

        // Check if user is admin or the trainer themselves
        if (!in_array($request->user()->role, ['admin', 'super_admin']) && 
            $request->user()->id !== $trainer->user_personal_trainer_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nickname' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'metadata' => 'nullable|array',
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
            $updateData = $request->only(['nickname', 'description', 'metadata']);

            // Handle image uploads
            if ($request->hasFile('images')) {
                // Delete old images
                if ($trainer->images) {
                    foreach ($trainer->images as $oldImage) {
                        Storage::disk('public')->delete($oldImage);
                    }
                }

                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('personal_trainers', 'public');
                    $imagePaths[] = $path;
                }
                $updateData['images'] = $imagePaths;
            }

            $trainer->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Personal trainer updated successfully',
                'data' => $trainer->load('userPersonalTrainer')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Trainer update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete personal trainer (admin only)
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
            $trainer = PersonalTrainer::findOrFail($id);
            
            // Check if trainer has active assignments
            $activeAssignments = PersonalTrainerAssignment::where('personal_trainer_id', $trainer->user_personal_trainer_id)
                                                        ->where('status', 'active')
                                                        ->count();
            
            if ($activeAssignments > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete trainer as they have active assignments'
                ], 400);
            }

            $trainer->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Personal trainer deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Trainer deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get trainer packages
     */
    public function packages(Request $request, $trainerId = null)
    {
        $query = PersonalTrainerPackage::with('personalTrainer.userPersonalTrainer');

        if ($trainerId) {
            $query->where('personal_trainer_id', $trainerId);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            // Default to active packages
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

        $packages = $query->orderBy('price', 'asc')
                         ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $packages
        ]);
    }

    /**
     * Get specific trainer package
     */
    public function showPackage($packageId)
    {
        $package = PersonalTrainerPackage::with('personalTrainer.userPersonalTrainer')
                                        ->findOrFail($packageId);

        return response()->json([
            'status' => 'success',
            'data' => $package
        ]);
    }

    /**
     * Create trainer package
     */
    public function createPackage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'personal_trainer_id' => 'required|exists:personal_trainers,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'day_duration' => 'required|integer|min:1',
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
            $trainer = PersonalTrainer::findOrFail($request->personal_trainer_id);

            // Check if user is admin or the trainer themselves
            if (!in_array($request->user()->role, ['admin', 'super_admin']) && 
                $request->user()->id !== $trainer->user_personal_trainer_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $packageData = $request->only(['personal_trainer_id', 'name', 'description', 'day_duration', 'price', 'status']);

            // Handle image uploads
            if ($request->hasFile('images')) {
                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('trainer_packages', 'public');
                    $imagePaths[] = $path;
                }
                $packageData['images'] = $imagePaths;
            }

            $package = PersonalTrainerPackage::create($packageData);

            return response()->json([
                'status' => 'success',
                'message' => 'Trainer package created successfully',
                'data' => $package->load('personalTrainer.userPersonalTrainer')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Package creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update trainer package
     */
    public function updatePackage(Request $request, $packageId)
    {
        $package = PersonalTrainerPackage::with('personalTrainer')->findOrFail($packageId);

        // Check if user is admin or the trainer themselves
        if (!in_array($request->user()->role, ['admin', 'super_admin']) && 
            $request->user()->id !== $package->personalTrainer->user_personal_trainer_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'day_duration' => 'sometimes|integer|min:1',
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
            $updateData = $request->only(['name', 'description', 'day_duration', 'price', 'status']);

            // Handle image uploads
            if ($request->hasFile('images')) {
                // Delete old images
                if ($package->images) {
                    foreach ($package->images as $oldImage) {
                        Storage::disk('public')->delete($oldImage);
                    }
                }

                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('trainer_packages', 'public');
                    $imagePaths[] = $path;
                }
                $updateData['images'] = $imagePaths;
            }

            $package->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Trainer package updated successfully',
                'data' => $package->load('personalTrainer.userPersonalTrainer')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Package update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete trainer package
     */
    public function deletePackage(Request $request, $packageId)
    {
        $package = PersonalTrainerPackage::with('personalTrainer')->findOrFail($packageId);

        // Check if user is admin or the trainer themselves
        if (!in_array($request->user()->role, ['admin', 'super_admin']) && 
            $request->user()->id !== $package->personalTrainer->user_personal_trainer_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        try {
            // Check if package has active assignments
            $activeAssignments = PersonalTrainerAssignment::where('personal_trainer_package_id', $packageId)
                                                        ->where('status', 'active')
                                                        ->count();
            
            if ($activeAssignments > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete package as it has active assignments'
                ], 400);
            }

            $package->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Trainer package deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Package deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get trainer assignments
     */
    public function assignments(Request $request, $trainerId = null)
    {
        $query = PersonalTrainerAssignment::with(['user', 'personalTrainerPackage']);

        if ($trainerId) {
            $query->where('personal_trainer_id', $trainerId);
        }

        // Check if user is requesting their own assignments as a trainer
        if (!$trainerId && $request->user()->role === 'trainer') {
            $query->where('personal_trainer_id', $request->user()->id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        $assignments = $query->orderBy('created_at', 'desc')
                           ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $assignments
        ]);
    }

    /**
     * Get specific assignment
     */
    public function showAssignment($assignmentId)
    {
        $assignment = PersonalTrainerAssignment::with([
            'user',
            'personalTrainer',
            'personalTrainerPackage',
            'personalTrainerSchedules'
        ])->findOrFail($assignmentId);

        return response()->json([
            'status' => 'success',
            'data' => $assignment
        ]);
    }

    /**
     * Purchase trainer package
     */
    public function purchasePackage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'personal_trainer_package_id' => 'required|exists:personal_trainer_packages,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $package = PersonalTrainerPackage::findOrFail($request->personal_trainer_package_id);
            
            if ($package->status !== 'active') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This trainer package is not available'
                ], 400);
            }

            // Check if user has active membership
            $user = $request->user();
            if ($user->membership_status !== 'active' || $user->membership_end_date < now()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Active membership required to purchase trainer packages'
                ], 400);
            }

            // This will redirect to payment process
            return response()->json([
                'status' => 'success',
                'message' => 'Please proceed to payment',
                'data' => [
                    'package' => $package->load('personalTrainer.userPersonalTrainer'),
                    'redirect_to' => 'payment_process'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Purchase failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get trainer schedules
     */
    public function schedules(Request $request, $trainerId = null)
    {
        $query = PersonalTrainerSchedule::with(['personalTrainerAssignment.user', 'personalTrainerAssignment.personalTrainerPackage']);

        if ($trainerId) {
            $query->whereHas('personalTrainerAssignment', function($q) use ($trainerId) {
                $q->where('personal_trainer_id', $trainerId);
            });
        }

        // Check if user is requesting their own schedules as a trainer
        if (!$trainerId && $request->user()->role === 'trainer') {
            $query->whereHas('personalTrainerAssignment', function($q) use ($request) {
                $q->where('personal_trainer_id', $request->user()->id);
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('scheduled_at', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('scheduled_at', '<=', $request->end_date);
        }

        $schedules = $query->orderBy('scheduled_at', 'asc')
                         ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $schedules
        ]);
    }

    /**
     * Create training schedule
     */
    public function createSchedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'personal_trainer_assignment_id' => 'required|exists:personal_trainer_assignments,id',
            'scheduled_at' => 'required|date|after:now',
            'status' => 'required|in:scheduled,completed,cancelled,missed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $assignment = PersonalTrainerAssignment::findOrFail($request->personal_trainer_assignment_id);

            // Check if user is admin, trainer, or the assigned member
            if (!in_array($request->user()->role, ['admin', 'super_admin']) && 
                $request->user()->id !== $assignment->personal_trainer_id &&
                $request->user()->id !== $assignment->user_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Check if assignment has remaining days
            if ($assignment->day_left <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No remaining training days in this assignment'
                ], 400);
            }

            $schedule = PersonalTrainerSchedule::create([
                'personal_trainer_assignment_id' => $assignment->id,
                'scheduled_at' => $request->scheduled_at,
                'status' => $request->status,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Training schedule created successfully',
                'data' => $schedule->load('personalTrainerAssignment')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Schedule creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update training schedule
     */
    public function updateSchedule(Request $request, $scheduleId)
    {
        $schedule = PersonalTrainerSchedule::with('personalTrainerAssignment')->findOrFail($scheduleId);

        // Check if user is admin, trainer, or the assigned member
        if (!in_array($request->user()->role, ['admin', 'super_admin']) && 
            $request->user()->id !== $schedule->personalTrainerAssignment->personal_trainer_id &&
            $request->user()->id !== $schedule->personalTrainerAssignment->user_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'scheduled_at' => 'sometimes|date',
            'status' => 'sometimes|in:scheduled,completed,cancelled,missed',
            'check_in_time' => 'nullable|date',
            'check_out_time' => 'nullable|date|after:check_in_time',
            'training_log' => 'nullable|array',
            'trainer_notes' => 'nullable|string',
            'member_feedback' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = $request->only([
                'scheduled_at', 'status', 'check_in_time', 'check_out_time',
                'training_log', 'trainer_notes', 'member_feedback'
            ]);

            // If marking as completed, decrement day_left
            if ($request->status === 'completed' && $schedule->status !== 'completed') {
                $assignment = $schedule->personalTrainerAssignment;
                if ($assignment->day_left > 0) {
                    $assignment->decrement('day_left');
                }
            }

            $schedule->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Training schedule updated successfully',
                'data' => $schedule->load('personalTrainerAssignment')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Schedule update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get trainer statistics
     */
    public function statistics(Request $request, $trainerId = null)
    {
        // If not admin and no specific trainer, get current user's stats
        if (!in_array($request->user()->role, ['admin', 'super_admin']) && !$trainerId) {
            if ($request->user()->role !== 'trainer') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ], 403);
            }
            $trainerId = $request->user()->id;
        }

        $query = PersonalTrainerAssignment::query();
        $scheduleQuery = PersonalTrainerSchedule::query();

        if ($trainerId) {
            $query->where('personal_trainer_id', $trainerId);
            $scheduleQuery->whereHas('personalTrainerAssignment', function($q) use ($trainerId) {
                $q->where('personal_trainer_id', $trainerId);
            });
        }

        $stats = [
            'total_assignments' => $query->count(),
            'active_assignments' => $query->where('status', 'active')->count(),
            'completed_assignments' => $query->where('status', 'completed')->count(),
            'total_sessions_this_month' => $scheduleQuery->whereMonth('scheduled_at', now()->month)
                                                        ->whereYear('scheduled_at', now()->year)
                                                        ->count(),
            'completed_sessions_this_month' => $scheduleQuery->where('status', 'completed')
                                                            ->whereMonth('scheduled_at', now()->month)
                                                            ->whereYear('scheduled_at', now()->year)
                                                            ->count(),
            'upcoming_sessions' => $scheduleQuery->where('status', 'scheduled')
                                                ->where('scheduled_at', '>', now())
                                                ->orderBy('scheduled_at', 'asc')
                                                ->take(5)
                                                ->with('personalTrainerAssignment.user')
                                                ->get()
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }
}