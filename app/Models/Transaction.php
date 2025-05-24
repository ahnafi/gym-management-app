<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\Relation;

class Transaction extends Pivot
{
    protected $table = 'transactions';

    protected $fillable = [
        'code',
        'amount',
        'payment_method',
        'midtrans_transaction_id',
        'payment_channel',
        'payment_token',
        'redirect_url',
        'payment_deadline',
        'payment_date',
        'payment_status',
        'raw_notification',
        'purchasable_type',
        'purchasable_id',
        'user_id',
    ];

    protected $casts = [
        'payment_deadline' => 'datetime',
        'payment_date' => 'datetime',
        'raw_notification' => 'array',
    ];

    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
