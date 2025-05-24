<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GymClassSchedule;
use Carbon\Carbon;

class GymClassScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $schedules = [
            [
                'date' => Carbon::today()->addDays(1),
                'start_time' => '08:00:00',
                'end_time' => '09:00:00',
                'slot' => 15,
                'gym_class_id' => 1,
            ],
            [
                'date' => Carbon::today()->addDays(2),
                'start_time' => '10:00:00',
                'end_time' => '11:30:00',
                'slot' => 20,
                'gym_class_id' => 2,
            ],
            [
                'date' => Carbon::today()->addDays(3),
                'start_time' => '14:00:00',
                'end_time' => '15:00:00',
                'slot' => 10,
                'gym_class_id' => 3,
            ],
            [
                'date' => Carbon::today()->addDays(4),
                'start_time' => '17:00:00',
                'end_time' => '18:00:00',
                'slot' => 12,
                'gym_class_id' => 4,
            ],
            [
                'date' => Carbon::today()->addDays(5),
                'start_time' => '09:00:00',
                'end_time' => '10:00:00',
                'slot' => 18,
                'gym_class_id' => 5,
            ],
            [
                'date' => Carbon::today()->addDays(6),
                'start_time' => '13:00:00',
                'end_time' => '14:30:00',
                'slot' => 16,
                'gym_class_id' => 6,
            ],
            [
                'date' => Carbon::today()->addDays(7),
                'start_time' => '07:00:00',
                'end_time' => '08:00:00',
                'slot' => 10,
                'gym_class_id' => 7,
            ],
            [
                'date' => Carbon::today()->addDays(8),
                'start_time' => '16:00:00',
                'end_time' => '17:00:00',
                'slot' => 14,
                'gym_class_id' => 8,
            ],
            [
                'date' => Carbon::today()->addDays(9),
                'start_time' => '18:00:00',
                'end_time' => '19:00:00',
                'slot' => 20,
                'gym_class_id' => 9,
            ],
            [
                'date' => Carbon::today()->addDays(10),
                'start_time' => '11:00:00',
                'end_time' => '12:00:00',
                'slot' => 15,
                'gym_class_id' => 10,
            ],
        ];

        foreach ($schedules as $schedule) {
            GymClassSchedule::create($schedule);
        }
    }
}
