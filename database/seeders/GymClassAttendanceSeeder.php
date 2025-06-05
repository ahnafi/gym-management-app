<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GymClassAttendance;
use Carbon\Carbon;

class GymClassAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $attendances = [
            [
                'status' => 'attended',
                'attended_at' => Carbon::now()->subDays(10),
                'user_id' => 1,
                'gym_class_schedule_id' => 1,
            ],
            [
                'status' => 'missed',
                'attended_at' => Carbon::now()->subDays(9),
                'user_id' => 2,
                'gym_class_schedule_id' => 2,
            ],
            [
                'status' => 'attended',
                'attended_at' => Carbon::now()->subDays(8),
                'user_id' => 3,
                'gym_class_schedule_id' => 3,
            ],
            [
                'status' => 'assigned',
                'attended_at' => Carbon::now()->subDays(7),
                'user_id' => 4,
                'gym_class_schedule_id' => 4,
            ],
            [
                'status' => 'attended',
                'attended_at' => Carbon::now()->subDays(6),
                'user_id' => 5,
                'gym_class_schedule_id' => 5,
            ],
            [
                'status' => 'missed',
                'attended_at' => Carbon::now()->subDays(5),
                'user_id' => 6,
                'gym_class_schedule_id' => 6,
            ],
            [
                'status' => 'assigned',
                'attended_at' => Carbon::now()->subDays(4),
                'user_id' => 7,
                'gym_class_schedule_id' => 7,
            ],
            [
                'status' => 'attended',
                'attended_at' => Carbon::now()->subDays(3),
                'user_id' => 8,
                'gym_class_schedule_id' => 8,
            ],
            [
                'status' => 'attended',
                'attended_at' => Carbon::now()->subDays(2),
                'user_id' => 9,
                'gym_class_schedule_id' => 9,
            ],
            [
                'status' => 'attended',
                'attended_at' => Carbon::now()->subDays(1),
                'user_id' => 10,
                'gym_class_schedule_id' => 10,
            ],
        ];

        foreach ($attendances as $attendance) {
            GymClassAttendance::create($attendance);
        }
    }
}
