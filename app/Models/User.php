<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'membership_registered',
        'membership_status',
        'membership_end_date',
        'profile_bio',
        'profile_image'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->profile_image)) {
                $model->profile_image = 'user_profile\/default-user_profile.jpg';
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('profile_image')) {
                $oldImage = $model->getOriginal('profile_image');
                $newImage = $model->profile_image;

                if (
                    $oldImage &&
                    $oldImage !== $newImage &&
                    $oldImage !== 'user_profile\/default-user_profile.jpg' &&
                    Storage::disk('public')->exists($oldImage)
                ) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });

        static::deleting(function ($model) {
            if (
                $model->profile_image &&
                $model->profile_image !== 'user_profile\/default-user_profile.jpg' &&
                Storage::disk('public')->exists($model->profile_image)
            ) {
                Storage::disk('public')->delete($model->profile_image);
            }
        });
    }



    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'personal-trainer') {
            return $this->role === 'trainer'; // or whatever condition you need
        }

        return str_ends_with($this->role, 'admin') && $this->hasVerifiedEmail();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function gymVisits(): HasMany
    {
        return $this->hasMany(GymVisit::class);
    }

    public function membershipHistories(): HasMany
    {
        return $this->hasMany(MembershipHistory::class);
    }

    public function gymClassAttendances(): HasMany
    {
        return $this->hasMany(GymClassAttendance::class);
    }

    public function personalTrainer(): HasOne
    {
        return $this->hasOne(PersonalTrainer::class);
    }

    public function personalTrainerAssignments(): HasMany
    {
        return $this->hasMany(PersonalTrainerAssignment::class);
    }
}
