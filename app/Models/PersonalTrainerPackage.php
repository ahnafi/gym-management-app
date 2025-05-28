<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class PersonalTrainerPackage extends Model
{
    protected $table = 'personal_trainer_packages';

    protected $fillable = [
        'code',
        'name',
        'slug',
        'description',
        'day_duration',
        'price',
        'images',
        'status',
        'personal_trainer_id'
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->code = 'PTP-' . str_pad($model->id, 3, '0', STR_PAD_LEFT);
            $model->slug = STR::slug($model->name, '-');
            $model->save();
        });

        static::updating(function ($model) {
            $model->slug = Str::slug($model->name, '-');
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
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
