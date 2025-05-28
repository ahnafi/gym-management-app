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
            $userId = $model->user_personal_trainer_id;
            $model->code = 'PT-' . str_pad($model->id, 3, '0', STR_PAD_LEFT) . str_pad($userId, 3, '0',STR_PAD_LEFT);
            $user = User::find($userId);
            $user->role = 'trainer';
            $user->save();

            $model->slug = STR::slug($model->nickname, '-');
            $model->save();
        });

        static::updating(function ($model) {
            $model->slug = Str::slug($model->nickname, '-');
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
