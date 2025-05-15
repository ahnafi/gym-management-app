<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MembershipPackage extends Model
{
    protected $table = 'membership_packages';

    protected $fillable = [
        'code',
        'name',
        'description',
        'duration',
        'status',
        'price',
        'images'
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function membershipHistories(): HasMany
    {
        return $this->hasMany(MembershipHistory::class);
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'purchasable');
    }
}
