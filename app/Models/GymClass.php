<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;


class GymClass extends Model
{
    protected $table = 'gym_classes';

    protected $fillable = [
        'code',
        'name',
        'slug',
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
            $model->slug = STR::slug($model->name, '-');
        });

        static::updating(function ($model) {
            $model->slug = Str::slug($model->name, '-');
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
