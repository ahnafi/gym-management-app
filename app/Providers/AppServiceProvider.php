<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'membership_package' => \App\Models\MembershipPackage::class,
            'gym_class' => \App\Models\GymClass::class,
            'personal_trainer_package' => \App\Models\PersonalTrainerPackage::class,
        ]);
    }
}
