<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GymClassSchedule extends Model
{
    protected $table = 'gym_class_schedules';

    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'slot',
        'available_slot',
        'gym_class_id'
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->available_slot = $model->slot;
        });
    }


    public function gymClass(): BelongsTo
    {
        return $this->belongsTo(GymClass::class);
    }

    public function gymClassAttendances(): HasMany
    {
        return $this->hasMany(GymClassAttendance::class);
    }
}
