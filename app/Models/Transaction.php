<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class Transaction extends Pivot
{
    protected $table = 'transactions';

    protected $fillable = [
        'code',
        'gym_class_date',
        'amount',
        'snap_token',
        'payment_date',
        'payment_status',
        'purchasable_type',
        'purchasable_id',
        'user_id',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $prefixMap = [
                'membership_package' => 'MP',
                'gym_class' => 'GC',
                'personal_trainer_package' => 'PTP',
            ];

            $type = $model->purchasable_type;

            if (str_contains($type, '\\')) {
                $type = array_search($type, Relation::getMorphedModelAliases()) ?: class_basename($type);
            }

            $prefix = $prefixMap[$type] ?? 'TX';
            $date = now()->format('Ymd');
            $random = strtoupper(Str::random(4));

            $userId = $model->user_id ?? '0';

            $model->code = "{$prefix}-{$date}-U{$userId}-{$random}";
        });
    }

    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
