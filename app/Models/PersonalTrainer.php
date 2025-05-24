<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class PersonalTrainer extends Model
{
    protected $table = 'personal_trainers';

    protected $fillable = [
        'code',
        'nickname',
        'description',
        'metadata',
        'images',
        'user_personal_trainer_id',
    ];

    protected $casts = [
        'metadata' => 'array',
        'images' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->code = 'PT-' . strtoupper(uniqid());
            $userId = $model->user_personal_trainer_id;
            $user = User::find($userId);
            $user->role = 'trainer';
            $user->save();
        });

        static::deleting(function ($model) {
            $user = User::find($model->user_personal_trainer_id);
            if ($user) {
                $user->role = 'member';
                $user->save();
            }
        });
    }

    public function userPersonalTrainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_personal_trainer_id');
    }

    public function personalTrainerPackage(): HasMany
    {
        return $this->hasMany(PersonalTrainerPackage::class, 'personal_trainer_id');
    }
}
