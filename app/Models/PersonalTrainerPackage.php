<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PersonalTrainerPackage extends Model
{
    protected $table = 'personal_trainer_packages';

    protected $fillable = [
        'code',
        'name',
        'description',
        'day_duration',
        'price',
        'images',
        'personal_trainer_id'
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->code = 'PTP-' . strtoupper(uniqid());
        });
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'purchasable');
    }

    public function personalTrainer(): BelongsTo
    {
        return $this->belongsTo(PersonalTrainer::class, 'personal_trainer_id');
    }

    public function personalTrainerAssignments(): HasMany
    {
        return $this->hasMany(PersonalTrainerAssignment::class);
    }
}
