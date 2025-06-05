<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PersonalTrainerAssignment;
use Carbon\Carbon;

class PersonalTrainerAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $assignments = [
            [
                'user_id' => 1,
                'personal_trainer_id' => 1,
                'personal_trainer_package_id' => 1,
                'day_left' => 7,
                'start_date' => Carbon::now()->subDays(1),
                'end_date' => Carbon::now()->addDays(6),
                'status' => 'active',
            ],
            [
                'user_id' => 5,
                'personal_trainer_id' => 2,
                'personal_trainer_package_id' => 2,
                'day_left' => 14,
                'start_date' => Carbon::now()->subDays(2),
                'end_date' => Carbon::now()->addDays(12),
                'status' => 'active',
            ],
            [
                'user_id' => 6,
                'personal_trainer_id' => 3,
                'personal_trainer_package_id' => 3,
                'day_left' => 25,
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(25),
                'status' => 'active',
            ],
            [
                'user_id' => 7,
                'personal_trainer_id' => 1,
                'personal_trainer_package_id' => 4,
                'day_left' => 5,
                'start_date' => Carbon::now()->subDays(2),
                'end_date' => Carbon::now()->addDays(3),
                'status' => 'active',
            ],
            [
                'user_id' => 8,
                'personal_trainer_id' => 2,
                'personal_trainer_package_id' => 5,
                'day_left' => 0,
                'start_date' => Carbon::now()->subDays(21),
                'end_date' => Carbon::now()->subDay(),
                'status' => 'completed',
            ],
            [
                'user_id' => 9,
                'personal_trainer_id' => 3,
                'personal_trainer_package_id' => 6,
                'day_left' => 10,
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(10),
                'status' => 'active',
            ],
            [
                'user_id' => 10,
                'personal_trainer_id' => 1,
                'personal_trainer_package_id' => 7,
                'day_left' => 3,
                'start_date' => Carbon::now()->subDays(7),
                'end_date' => Carbon::now()->addDays(3),
                'status' => 'active',
            ],
            [
                'user_id' => 11,
                'personal_trainer_id' => 2,
                'personal_trainer_package_id' => 8,
                'day_left' => 5,
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->addDays(5),
                'status' => 'cancelled',
            ],
            [
                'user_id' => 12,
                'personal_trainer_id' => 3,
                'personal_trainer_package_id' => 9,
                'day_left' => 15,
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(15),
                'status' => 'active',
            ],
            [
                'user_id' => 13,
                'personal_trainer_id' => 1,
                'personal_trainer_package_id' => 10,
                'day_left' => 7,
                'start_date' => Carbon::now()->subDays(1),
                'end_date' => Carbon::now()->addDays(7),
                'status' => 'active',
            ],
        ];

        foreach ($assignments as $data) {
            PersonalTrainerAssignment::create($data);
        }
    }
}
