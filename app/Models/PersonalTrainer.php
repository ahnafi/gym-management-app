<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;


class PersonalTrainer extends Model
{
    protected $table = 'personal_trainers';

    protected $fillable = [
        'code',
        'nickname',
        'slug',
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

        static::created(function ($model) {
            $model->code = 'PT-' . $model->user_personal_trainer_id . $model->id;
            $model->slug = Str::slug($model->nickname ?? 'trainer', '-');
            $model->saveQuietly();

            $user = User::find($model->user_personal_trainer_id);
            if ($user && $user->role !== 'trainer') {
                $user->role = 'trainer';
                $user->save();
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('nickname')) {
                $model->slug = Str::slug($model->nickname ?? 'trainer', '-');
            }
        });

        static::deleting(function ($model) {
            $user = User::find($model->user_personal_trainer_id);
            if ($user && $user->role === 'trainer') {
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
