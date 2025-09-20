<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class PersonalTrainer extends Model
{
    use HasFactory;
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

        static::creating(function ($model) {
            $model->slug = Str::slug($model->nickname ?? 'trainer', '-');
        });

        static::created(function ($model) {
            $model->code = 'PT-' . $model->user_personal_trainer_id . $model->id;
            $model->saveQuietly();

            $user = User::find($model->user_personal_trainer_id);
            if ($user && $user->role !== 'trainer') {
                $user->role = 'trainer';
                $user->saveQuietly();
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('nickname')) {
                $model->slug = Str::slug($model->nickname ?? 'trainer', '-');
            }

            if ($model->isDirty('images')) {
                $originalImages = $model->getOriginal('images') ?? [];
                $newImages = $model->images ?? [];

                $removedImages = array_diff($originalImages, $newImages);

                foreach ($removedImages as $removedImage) {
                    if (Storage::disk('public')->exists($removedImage)) {
                        Storage::disk('public')->delete($removedImage);
                    }
                }
            }
        });

        static::deleting(function ($model) {
            $user = User::find($model->user_personal_trainer_id);
            if ($user && $user->role === 'trainer') {
                $user->role = 'member';
                $user->save();
            }

            if (!empty($model->images)) {
                foreach ($model->images as $filename) {
                    if (Storage::disk('public')->exists($filename)) {
                        Storage::disk('public')->delete($filename);
                    }
                }
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
