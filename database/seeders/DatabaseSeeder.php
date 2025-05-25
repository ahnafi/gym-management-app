<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            UserSeeder::class,
            PersonalTrainerSeeder::class,
            GymVisitSeeder::class,
            MembershipPackageSeeder::class,
            MembershipHistorySeeder::class,
            GymClassSeeder::class,
            GymClassScheduleSeeder::class,
            GymClassAttendanceSeeder::class,
            PersonalTrainerPackageSeeder::class,
            PersonalTrainerAssignmentSeeder::class,
            PersonalTrainerScheduleSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
