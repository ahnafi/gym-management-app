<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GymClassAttendance extends Model
{
    use HasFactory;
    protected $table = 'gym_class_attendances';

    protected $fillable = [
        'status',
        'attended_at',
        'user_id',
        'gym_class_schedule_id'
    ];

    protected $casts = [
        'attended_at' => 'datetime',
    ];

    public function gymClassSchedule(): BelongsTo
    {
        return $this->belongsTo(GymClassSchedule::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
