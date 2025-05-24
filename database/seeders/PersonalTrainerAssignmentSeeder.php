<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PersonalTrainerAssignment;
use App\Models\User;
use App\Models\PersonalTrainerPackage;
use Carbon\Carbon;

class PersonalTrainerAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil 10 user role member
        $members = User::where('role', 'member')->inRandomOrder()->take(10)->get();

        // Ambil semua trainer
        $trainers = User::where('role', 'trainer')->pluck('id')->toArray();

        // Ambil semua package
        $packages = PersonalTrainerPackage::pluck('id')->toArray();

        $statuses = ['active', 'cancelled', 'completed'];

        $assignments = [];

        foreach ($members as $member) {
            $startDate = Carbon::now()->subDays(rand(1, 10));
            $duration = rand(5, 20);
            $endDate = $startDate->copy()->addDays($duration);
            $dayLeft = rand(0, $duration);

            $assignments[] = [
                'day_left' => $dayLeft,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $statuses[array_rand($statuses)],
                'user_id' => $member->id,
                'personal_trainer_id' => $trainers[array_rand($trainers)],
                'personal_trainer_package_id' => $packages[array_rand($packages)],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        PersonalTrainerAssignment::insert($assignments);
    }
}
