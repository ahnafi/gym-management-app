<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Services\FileNaming;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MembershipPackage extends Model
{
    protected $table = 'membership_packages';

    protected $fillable = [
        'code',
        'name',
        'slug',
        'description',
        'duration',
        'status',
        'price',
        'images',
        'images_path'
    ];

    protected $casts = [
        'images' => 'array',
        'images_path' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->slug = Str::slug($model->name, '-');
        });

        static::created(function ($model) {
            $model->code = 'MP-' . str_pad($model->id, 3, '0', STR_PAD_LEFT);
            DB::table('membership_packages')
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


    public function membershipHistories(): HasMany
    {
        return $this->hasMany(MembershipHistory::class);
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'purchasable');
    }
}
