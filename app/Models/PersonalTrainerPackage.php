<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PersonalTrainerPackage extends Model
{
    use HasFactory;
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

        static::creating(function ($model) {
            $model->slug = Str::slug($model->name, '-');
        });

        static::created(function ($model) {
            $model->code = 'PTP-' . str_pad($model->id, 3, '0', STR_PAD_LEFT);
            DB::table('personal_trainer_packages')
                ->where('id', $model->id)
                ->update(['code' => $model->code]);
        });

        static::updating(function ($model) {
            if ($model->isDirty('name')) {
                $model->slug = Str::slug($model->name, '-');
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
            if (!empty($model->images)) {
                foreach ($model->images as $filename) {
                    if (Storage::disk('public')->exists($filename)) {
                        Storage::disk('public')->delete($filename);
                    }
                }
            }
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
