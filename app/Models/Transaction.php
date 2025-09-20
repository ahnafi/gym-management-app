<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class Transaction extends Pivot
{
    use HasFactory;
    protected $table = 'transactions';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
            'code',
            'gym_class_schedule_id',
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
            $type = $model->purchasable_type;

            if (str_contains($type, '\\')) {
                $type = array_search($type, Relation::getMorphedModelAliases()) ?: class_basename($type);
            }

            // Load the purchasable relation if not already loaded
            if (!$model->relationLoaded('purchasable')) {
                $model->load('purchasable');
            }

            $prefix = $model->purchasable->code ?? 'TX';
            $date = now()->format('Ymd');
            $random = strtoupper(Str::random(4));

            $userId = $model->user_id ?? '0';

            $model->code = "T{$prefix}-{$date}-U{$userId}-{$random}";
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
