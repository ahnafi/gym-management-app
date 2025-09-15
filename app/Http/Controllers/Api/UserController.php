<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Get all users (admin only)
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

        $query = User::query();

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by membership status
        if ($request->has('membership_status')) {
            $query->where('membership_status', $request->membership_status);
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->with([
            'membershipHistories.membership_package',
            'personalTrainer'
        ])->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    /**
     * Get specific user (admin only)
     */
    public function show(Request $request, $id)
    {
        // Check if user is admin or requesting own profile
        if (!in_array($request->user()->role, ['admin', 'super_admin']) && $request->user()->id != $id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $user = User::with([
            'transactions.purchasable',
            'gymVisits',
            'membershipHistories.membership_package',
            'gymClassAttendances.gymClassSchedule.gymClass',
            'personalTrainerAssignments.personalTrainerPackage',
            'personalTrainer.personalTrainerPackage'
        ])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $user->makeHidden(['password', 'remember_token'])
        ]);
    }

    /**
     * Create new user (admin only)
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
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:member,trainer,admin,super_admin',
            'profile_bio' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userData = $request->only(['name', 'email', 'phone', 'role', 'profile_bio']);
            $userData['password'] = Hash::make($request->password);
            $userData['email_verified_at'] = now(); // Admin created users are auto-verified

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                $path = $request->file('profile_image')->store('user_profile', 'public');
                $userData['profile_image'] = $path;
            }

            $user = User::create($userData);

            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'data' => $user->makeHidden(['password', 'remember_token'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user (admin only or own profile)
     */
    public function update(Request $request, $id)
    {
        // Check if user is admin or updating own profile
        if (!in_array($request->user()->role, ['admin', 'super_admin']) && $request->user()->id != $id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $user = User::findOrFail($id);

        $rules = [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'profile_bio' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Only admin can update role and membership status
        if (in_array($request->user()->role, ['admin', 'super_admin'])) {
            $rules['role'] = 'sometimes|in:member,trainer,admin,super_admin';
            $rules['membership_status'] = 'sometimes|in:active,inactive,expired';
            $rules['membership_end_date'] = 'nullable|date';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = $request->only(['name', 'email', 'phone', 'profile_bio']);

            // Only admin can update these fields
            if (in_array($request->user()->role, ['admin', 'super_admin'])) {
                $adminFields = $request->only(['role', 'membership_status', 'membership_end_date']);
                $updateData = array_merge($updateData, $adminFields);
            }

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                // Delete old image if exists and not default
                if ($user->profile_image && $user->profile_image !== 'user_profile/default-user_profile.jpg') {
                    Storage::disk('public')->delete($user->profile_image);
                }
                
                $path = $request->file('profile_image')->store('user_profile', 'public');
                $updateData['profile_image'] = $path;
            }

            $user->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'User updated successfully',
                'data' => $user->makeHidden(['password', 'remember_token'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user (admin only)
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

        // Prevent self-deletion
        if ($request->user()->id == $id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete your own account'
            ], 400);
        }

        try {
            $user = User::findOrFail($id);
            $user->delete(); // Soft delete

            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user statistics (admin only)
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
            'total_users' => User::count(),
            'active_members' => User::where('membership_status', 'active')->count(),
            'trainers' => User::where('role', 'trainer')->count(),
            'new_registrations_today' => User::whereDate('created_at', today())->count(),
            'new_registrations_this_month' => User::whereMonth('created_at', now()->month)
                                                 ->whereYear('created_at', now()->year)
                                                 ->count(),
            'users_by_role' => User::selectRaw('role, COUNT(*) as count')
                                  ->groupBy('role')
                                  ->pluck('count', 'role'),
            'membership_status_distribution' => User::selectRaw('membership_status, COUNT(*) as count')
                                                   ->groupBy('membership_status')
                                                   ->pluck('count', 'membership_status')
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }

    /**
     * Get trainers list
     */
    public function trainers(Request $request)
    {
        $trainers = User::where('role', 'trainer')
                       ->with('personalTrainer.personalTrainerPackage')
                       ->get()
                       ->makeHidden(['password', 'remember_token']);

        return response()->json([
            'status' => 'success',
            'data' => $trainers
        ]);
    }
}