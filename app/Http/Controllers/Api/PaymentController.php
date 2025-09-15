<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\MembershipPackage;
use App\Models\GymClass;
use App\Models\PersonalTrainerPackage;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Get all transactions (admin only)
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

        $query = Transaction::with(['user', 'purchasable']);

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by purchasable type
        if ($request->has('purchasable_type')) {
            $query->where('purchasable_type', $request->purchasable_type);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }

        // Search by transaction code or user
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                               ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $transactions = $query->orderBy('created_at', 'desc')
                            ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ]);
    }

    /**
     * Get user's own transactions
     */
    public function myTransactions(Request $request)
    {
        $user = $request->user();

        $query = Transaction::where('user_id', $user->id)->with('purchasable');

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by purchasable type
        if ($request->has('purchasable_type')) {
            $query->where('purchasable_type', $request->purchasable_type);
        }

        $transactions = $query->orderBy('created_at', 'desc')
                            ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ]);
    }

    /**
     * Get specific transaction
     */
    public function show(Request $request, $id)
    {
        $transaction = Transaction::with(['user', 'purchasable'])->findOrFail($id);

        // Check if user is admin or transaction owner
        if (!in_array($request->user()->role, ['admin', 'super_admin']) && 
            $request->user()->id !== $transaction->user_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $transaction
        ]);
    }

    /**
     * Initiate payment for membership package
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

            $transaction = $this->paymentService->initiatePayment([
                'purchasable_type' => 'membership_package',
                'purchasable_id' => $package->id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment initiated successfully',
                'data' => [
                    'transaction' => $transaction,
                    'snap_token' => $transaction->snap_token,
                    'package' => $package
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment initiation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Initiate payment for gym class
     */
    public function purchaseGymClass(Request $request)
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
            $gymClass = GymClass::findOrFail($request->gym_class_id);
            
            if ($gymClass->status !== 'active') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This gym class is not available'
                ], 400);
            }

            // Check if user has active membership
            $user = $request->user();
            if ($user->membership_status !== 'active' || $user->membership_end_date < now()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Active membership required to purchase gym classes'
                ], 400);
            }

            $transaction = $this->paymentService->initiatePayment([
                'purchasable_type' => 'gym_class',
                'purchasable_id' => $gymClass->id,
                'gym_class_schedule_id' => $request->gym_class_schedule_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment initiated successfully',
                'data' => [
                    'transaction' => $transaction,
                    'snap_token' => $transaction->snap_token,
                    'gym_class' => $gymClass
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment initiation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Initiate payment for personal trainer package
     */
    public function purchaseTrainerPackage(Request $request)
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

            $transaction = $this->paymentService->initiatePayment([
                'purchasable_type' => 'personal_trainer_package',
                'purchasable_id' => $package->id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment initiated successfully',
                'data' => [
                    'transaction' => $transaction,
                    'snap_token' => $transaction->snap_token,
                    'package' => $package->load('personalTrainer.userPersonalTrainer')
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment initiation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle payment notification from Midtrans
     */
    public function handleNotification(Request $request)
    {
        try {
            // Midtrans configuration
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.isProduction', false);

            $notification = new \Midtrans\Notification();

            $transactionCode = $notification->order_id;
            $paymentType = $notification->payment_type;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;

            Log::info('Payment notification received', [
                'order_id' => $transactionCode,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'payment_type' => $paymentType
            ]);

            $transaction = Transaction::where('code', $transactionCode)->first();

            if (!$transaction) {
                Log::error('Transaction not found', ['order_id' => $transactionCode]);
                return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
            }

            $paymentStatus = 'pending';

            if ($transactionStatus === 'capture') {
                if ($paymentType === 'credit_card') {
                    if ($fraudStatus === 'challenge') {
                        $paymentStatus = 'challenge';
                    } else if ($fraudStatus === 'accept') {
                        $paymentStatus = 'paid';
                    }
                }
            } else if ($transactionStatus === 'settlement') {
                $paymentStatus = 'paid';
            } else if ($transactionStatus === 'pending') {
                $paymentStatus = 'pending';
            } else if ($transactionStatus === 'deny') {
                $paymentStatus = 'failed';
            } else if ($transactionStatus === 'expire') {
                $paymentStatus = 'expired';
            } else if ($transactionStatus === 'cancel') {
                $paymentStatus = 'cancelled';
            }

            // Update payment status
            if ($paymentStatus === 'paid') {
                $this->paymentService->updatePaymentStatus($transaction->id, $paymentStatus);
            } else {
                $transaction->update(['payment_status' => $paymentStatus]);
            }

            Log::info('Payment status updated', [
                'transaction_id' => $transaction->id,
                'status' => $paymentStatus
            ]);

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Payment notification error', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Notification processing failed'
            ], 500);
        }
    }

    /**
     * Check payment status
     */
    public function checkStatus(Request $request, $transactionId)
    {
        try {
            $transaction = Transaction::findOrFail($transactionId);

            // Check if user is admin or transaction owner
            if (!in_array($request->user()->role, ['admin', 'super_admin']) && 
                $request->user()->id !== $transaction->user_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Check status with Midtrans if pending
            if ($transaction->payment_status === 'pending' && $transaction->snap_token) {
                \Midtrans\Config::$serverKey = config('midtrans.server_key');
                \Midtrans\Config::$isProduction = config('midtrans.isProduction', false);

                try {
                    $status = \Midtrans\Transaction::status($transaction->code);
                    
                    $paymentStatus = 'pending';
                    $statusData = (array) $status;
                    $transactionStatus = $statusData['transaction_status'] ?? 'pending';
                    
                    if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
                        $paymentStatus = 'paid';
                        $this->paymentService->updatePaymentStatus($transaction->id, $paymentStatus);
                    } else if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                        $paymentStatus = $transactionStatus === 'deny' ? 'failed' : $transactionStatus;
                        $transaction->update(['payment_status' => $paymentStatus]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to check Midtrans status', [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $transaction->refresh();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'transaction' => $transaction->load('purchasable'),
                    'payment_status' => $transaction->payment_status
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Status check failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel transaction
     */
    public function cancel(Request $request, $transactionId)
    {
        try {
            $transaction = Transaction::findOrFail($transactionId);

            // Check if user is admin or transaction owner
            if (!in_array($request->user()->role, ['admin', 'super_admin']) && 
                $request->user()->id !== $transaction->user_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Can only cancel pending transactions
            if ($transaction->payment_status !== 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Can only cancel pending transactions'
                ], 400);
            }

            $transaction->update([
                'payment_status' => 'cancelled'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction cancelled successfully',
                'data' => $transaction
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction cancellation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manual payment approval (admin only)
     */
    public function manualApproval(Request $request, $transactionId)
    {
        // Check if user is admin
        if (!in_array($request->user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:paid,failed',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $transaction = Transaction::findOrFail($transactionId);

            if ($request->status === 'paid') {
                $this->paymentService->updatePaymentStatus($transaction->id, 'paid');
            } else {
                $transaction->update(['payment_status' => 'failed']);
            }

            Log::info('Manual payment approval', [
                'transaction_id' => $transaction->id,
                'admin_id' => $request->user()->id,
                'status' => $request->status,
                'notes' => $request->notes
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment status updated successfully',
                'data' => $transaction->refresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Manual approval failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment statistics (admin only)
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
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        $stats = [
            'total_transactions' => Transaction::count(),
            'pending_transactions' => Transaction::where('payment_status', 'pending')->count(),
            'paid_transactions' => Transaction::where('payment_status', 'paid')->count(),
            'failed_transactions' => Transaction::where('payment_status', 'failed')->count(),
            'revenue_today' => Transaction::where('payment_status', 'paid')
                                        ->whereDate('payment_date', $today)
                                        ->sum('amount'),
            'revenue_this_month' => Transaction::where('payment_status', 'paid')
                                             ->where('payment_date', '>=', $thisMonth)
                                             ->sum('amount'),
            'revenue_last_month' => Transaction::where('payment_status', 'paid')
                                             ->where('payment_date', '>=', $lastMonth)
                                             ->where('payment_date', '<', $thisMonth)
                                             ->sum('amount'),
            'transactions_by_type' => Transaction::selectRaw('purchasable_type, COUNT(*) as count, SUM(amount) as total_amount')
                                               ->where('payment_status', 'paid')
                                               ->groupBy('purchasable_type')
                                               ->get(),
            'daily_revenue_this_month' => Transaction::selectRaw('DATE(payment_date) as date, SUM(amount) as revenue')
                                                   ->where('payment_status', 'paid')
                                                   ->where('payment_date', '>=', $thisMonth)
                                                   ->groupBy('date')
                                                   ->orderBy('date', 'asc')
                                                   ->get(),
            'top_spenders' => Transaction::selectRaw('user_id, SUM(amount) as total_spent')
                                       ->where('payment_status', 'paid')
                                       ->groupBy('user_id')
                                       ->orderBy('total_spent', 'desc')
                                       ->with('user:id,name,email')
                                       ->take(10)
                                       ->get()
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }
}