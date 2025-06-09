<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\GymClass;
use App\Models\MembershipPackage;
use App\Models\PersonalTrainerPackage;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;

use Inertia\Inertia;
use Illuminate\Validation\Rule;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;

class PaymentController extends Controller
{
    public function index()
    {
        $transactions = Transaction::query()
            ->where('user_id', auth()->id())
            ->select([
                'id',
                'code',
                'amount',
                'snap_token',
                'payment_date',
                'payment_status',
                'purchasable_type',
                'purchasable_id',
                'gym_class_schedule_id',
                'user_id',
                'created_at',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $membershipIds = $transactions->where('purchasable_type', 'membership_package')->pluck('purchasable_id')->unique()->all();
        $gymClassIds = $transactions->where('purchasable_type', 'gym_class')->pluck('purchasable_id')->unique()->all();
        $ptPackageIds = $transactions->where('purchasable_type', 'personal_trainer_package')->pluck('purchasable_id')->unique()->all();

        $memberships = MembershipPackage::whereIn('id', $membershipIds)->select('id', 'code', 'name')->get()->keyBy('id');
        $gymClasses = GymClass::whereIn('id', $gymClassIds)->select('id', 'code', 'name')->get()->keyBy('id');
        $ptPackages = PersonalTrainerPackage::whereIn('id', $ptPackageIds)->select('id', 'code', 'name')->get()->keyBy('id');

        $scheduleIds = $transactions->pluck('gym_class_schedule_id')->filter()->unique()->all();
        $schedules = DB::table('gym_class_schedules')
            ->whereIn('id', $scheduleIds)
            ->select('id', 'date', 'start_time', 'end_time')
            ->get()
            ->keyBy('id');

        $payments = $transactions->map(function ($transaction) use ($memberships, $gymClasses, $ptPackages, $schedules) {
            switch ($transaction->purchasable_type) {
                case 'membership_package':
                    $purchasable = $memberships->get($transaction->purchasable_id);
                    break;
                case 'gym_class':
                    $purchasable = $gymClasses->get($transaction->purchasable_id);
                    break;
                case 'personal_trainer_package':
                    $purchasable = $ptPackages->get($transaction->purchasable_id);
                    break;
                default:
                    $purchasable = null;
            }

            $schedule = $schedules->get($transaction->gym_class_schedule_id);

            return [
                'id' => $transaction->id,
                'code' => $transaction->code,
                'amount' => $transaction->amount,
                'snap_token' => $transaction->snap_token,
                'payment_date' => $transaction->payment_date,
                'payment_status' => $transaction->payment_status,
                'purchasable_type' => $transaction->purchasable_type,
                'purchasable_id' => $transaction->purchasable_id,
                'user_id' => $transaction->user_id,
                'created_at' => $transaction->created_at,

                'purchasable_name' => $purchasable?->name ?? '-',
                'purchasable_code' => $purchasable?->code ?? '-',
                'gym_class_schedule' => $schedule
                    ? [
                        'date' => $schedule->date,
                        'start_time' => $schedule->start_time,
                        'end_time' => $schedule->end_time,
                    ]
                    : null,
            ];
        });

        $membershipPackages = MembershipPackage::select(['id', 'name'])->get();
        $gymClasses = GymClass::select(['id', 'name'])->get();
        $personalTrainerPackages = PersonalTrainerPackage::select(['id', 'name'])->get();

        $purchasables = [
            ...$membershipPackages->toArray(),
            ...$gymClasses->toArray(),
            ...$personalTrainerPackages->toArray(),
        ];

        return Inertia::render('payment/index', [
            'payments' => $payments,
            'purchasables' => $purchasables,
        ]);
    }

    public function checkout(CheckoutRequest $request)
    {
        $validated = $request->validated();

        app(PaymentService::class)->initiatePayment($validated);

        return redirect()->route('payments.index')->with('alert', [
            'message' => 'Pembayaran berhasil diinisialiasi!',
            'type' => 'success',
        ]);

    }

    public function updatePaymentStatus(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => [
                'required',
                'exists:transactions,id',
            ],
            'status' => 'required',
        ]);

        app(PaymentService::class)->updatePaymentStatus($validated['transaction_id'], $validated['status']);
    }
}
