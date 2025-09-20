<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MembershipHistory extends Pivot
{
    use HasFactory, SoftDeletes;

    protected $table = 'membership_histories';

    protected $fillable = [
        'code',
        'start_date',
        'end_date',
        'status',
        'user_id',
        'membership_package_id'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $userId = $model->user_id;

            $lastHistory = self::where('user_id', $userId)
                ->orderBy('id', 'desc')
                ->first();

            $lastNumber = 0;

            if ($lastHistory && preg_match('/^MH' . $userId . '(\d+)$/', $lastHistory->code, $matches)) {
                $lastNumber = (int) $matches[1];
            }

            $nextNumber = $lastNumber + 1;

            $model->code = 'MH' . $userId . $nextNumber;
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membership_package(): BelongsTo
    {
        return $this->belongsTo(MembershipPackage::class);
    }
}
