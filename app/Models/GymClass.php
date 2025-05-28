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
        'status',
        'images'
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->code = 'GC-' . strtoupper(uniqid());
        });
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'purchasable');
    }

    public function gymClassSchedules(): HasMany
    {
        return $this->hasMany(GymClassSchedule::class);
    }
}
