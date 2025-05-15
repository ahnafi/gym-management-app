<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PersonalTrainerAssignment extends Model
{
    protected $table = 'personal_trainer_assignments';

    protected $fillable = [
        'start_date',
        'end_date',
        'status',
        'user_id',
        'personal_trainer_id',
        'personal_trainer_package_id'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function personalTrainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'personal_trainer_id');
    }

    public function personalTrainerPackage(): BelongsTo
    {
        return $this->belongsTo(PersonalTrainerPackage::class, 'personal_trainer_package_id');
    }

    public function personalTrainerScheules(): HasMany
    {
        return  $this->hasMany(PersonalTrainerSchedule::class, 'personal_trainer_assignment_id');
    }
}
