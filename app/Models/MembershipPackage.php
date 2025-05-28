<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

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
        'images'
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->code = 'MP-' . str_pad($model->id, 3, '0', STR_PAD_LEFT);
            $model->slug = STR::slug($model->name, '-');
        });

        static::updating(function ($model) {
            $model->slug = Str::slug($model->name, '-');
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
