<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class GymClass extends Model
{
    protected $table = 'gym_classes';

    protected $fillable = [
        'code',
        'name',
        'description',
        'price',
        'images'
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'purchasable');
    }

    public function gymClassSchedules(): HasMany
    {
        return $this->hasMany(GymClassSchedule::class);
    }
}
