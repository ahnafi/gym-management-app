<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MembershipPackage;
use App\Models\MembershipHistory;
use App\Services\AssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MembershipController extends Controller
{
    /**
     * Get all membership packages
     */
    public function packages(Request $request)
    {
        $query = MembershipPackage::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            // Default to active packages for public access
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

        // Sort by price or duration
        if ($request->has('sort_by')) {
            $sortBy = $request->sort_by;
            $sortOrder = $request->get('sort_order', 'asc');
            
            if (in_array($sortBy, ['price', 'duration', 'created_at'])) {
                $query->orderBy($sortBy, $sortOrder);
            }
        } else {
            $query->orderBy('price', 'asc');
        }

        $packages = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $packages
        ]);
    }

    /**
     * Get specific membership package
     */
    public function showPackage($id)
    {
        $package = MembershipPackage::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $package
        ]);
    }

    /**
     * Create membership package (admin only)
     */
    public function createPackage(Request $request)
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
            'duration' => 'required|integer|min:1',
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
            $packageData = $request->only(['name', 'description', 'duration', 'price', 'status']);

            // Handle image uploads
            if ($request->hasFile('images')) {
                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('membership_packages', 'public');
                    $imagePaths[] = $path;
                }
                $packageData['images'] = $imagePaths;
            }

            $package = MembershipPackage::create($packageData);

            return response()->json([
                'status' => 'success',
                'message' => 'Membership package created successfully',
                'data' => $package
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Package creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update membership package (admin only)
     */
    public function updatePackage(Request $request, $id)
    {
        // Check if user is admin
        if (!in_array($request->user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $package = MembershipPackage::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'duration' => 'sometimes|integer|min:1',
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
            $updateData = $request->only(['name', 'description', 'duration', 'price', 'status']);

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
                    $path = $image->store('membership_packages', 'public');
                    $imagePaths[] = $path;
                }
                $updateData['images'] = $imagePaths;
            }

            $package->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Membership package updated successfully',
                'data' => $package
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Package update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete membership package (admin only)
     */
    public function deletePackage(Request $request, $id)
    {
        // Check if user is admin
        if (!in_array($request->user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        try {
            $package = MembershipPackage::findOrFail($id);
            
            // Check if package is being used
            $activeHistories = MembershipHistory::where('membership_package_id', $id)
                                              ->where('status', 'active')
                                              ->count();
            
            if ($activeHistories > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete package as it has active memberships'
                ], 400);
            }

            $package->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Membership package deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Package deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's membership history
     */
    public function myMemberships(Request $request)
    {
        $user = $request->user();

        $memberships = MembershipHistory::where('user_id', $user->id)
                                      ->with('membership_package')
                                      ->orderBy('created_at', 'desc')
                                      ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $memberships
        ]);
    }

    /**
     * Get current active membership
     */
    public function currentMembership(Request $request)
    {
        $user = $request->user();

        $currentMembership = MembershipHistory::where('user_id', $user->id)
                                            ->where('status', 'active')
                                            ->where('end_date', '>', now())
                                            ->with('membership_package')
                                            ->first();

        if (!$currentMembership) {
            return response()->json([
                'status' => 'success',
                'message' => 'No active membership found',
                'data' => null
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $currentMembership
        ]);
    }

    /**
     * Purchase membership package
     */
    public function purchaseMembership(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'membership_package_id' => 'required|exists:membership_packages,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $package = MembershipPackage::findOrFail($request->membership_package_id);
            
            if ($package->status !== 'active') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This membership package is not available'
                ], 400);
            }

            // For registration package (MP-001), directly assign membership
            if ($package->code === 'MP-001') {
                AssignmentService::updateMembership($request->user()->id, $package->id);
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Registration completed successfully'
                ]);
            }

            // For other packages, create transaction (will be handled by PaymentController)
            return response()->json([
                'status' => 'success',
                'message' => 'Please proceed to payment',
                'data' => [
                    'package' => $package,
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
     * Get all membership histories (admin only)
     */
    public function allMemberships(Request $request)
    {
        // Check if user is admin
        if (!in_array($request->user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $query = MembershipHistory::with(['user', 'membership_package']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by package
        if ($request->has('package_id')) {
            $query->where('membership_package_id', $request->package_id);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        // Search by user name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $memberships = $query->orderBy('created_at', 'desc')
                           ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $memberships
        ]);
    }

    /**
     * Update membership status (admin only)
     */
    public function updateMembershipStatus(Request $request, $id)
    {
        // Check if user is admin
        if (!in_array($request->user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,expired,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $membership = MembershipHistory::findOrFail($id);
            $membership->update(['status' => $request->status]);

            // Update user's membership status if this is the current membership
            if ($request->status === 'expired' || $request->status === 'cancelled') {
                $user = $membership->user;
                if ($user->membership_end_date && $user->membership_end_date->equalTo($membership->end_date)) {
                    $user->update(['membership_status' => $request->status]);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Membership status updated successfully',
                'data' => $membership
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Status update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get membership statistics (admin only)
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
            'total_memberships' => MembershipHistory::count(),
            'active_memberships' => MembershipHistory::where('status', 'active')->count(),
            'expired_memberships' => MembershipHistory::where('status', 'expired')->count(),
            'revenue_this_month' => MembershipHistory::whereMonth('created_at', now()->month)
                                                   ->whereYear('created_at', now()->year)
                                                   ->join('membership_packages', 'membership_histories.membership_package_id', '=', 'membership_packages.id')
                                                   ->sum('membership_packages.price'),
            'popular_packages' => MembershipPackage::withCount('membershipHistories')
                                                 ->orderBy('membership_histories_count', 'desc')
                                                 ->take(5)
                                                 ->get(),
            'expiring_soon' => MembershipHistory::where('status', 'active')
                                              ->where('end_date', '>', now())
                                              ->where('end_date', '<=', now()->addDays(7))
                                              ->with(['user', 'membership_package'])
                                              ->get()
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }
}