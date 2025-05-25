<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PersonalTrainerSchedule;
use Carbon\Carbon;

class PersonalTrainerScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $schedules = [
            [
                'scheduled_at' => Carbon::now()->subDays(5),
                'status' => 'completed',
                'check_in_time' => '08:00:00',
                'check_out_time' => '09:00:00',
                'training_log' => json_encode(['squat' => '3x12', 'push_up' => '3x15']),
                'trainer_notes' => 'Good stamina',
                'member_feedback' => 'Intens and helpful',
                'personal_trainer_assignment_id' => 1,
            ],
            [
                'scheduled_at' => Carbon::now()->subDays(4),
                'status' => 'completed',
                'check_in_time' => '09:00:00',
                'check_out_time' => '10:00:00',
                'training_log' => json_encode(['running' => '20min', 'plank' => '3x60s']),
                'trainer_notes' => 'Needs improvement on balance',
                'member_feedback' => 'Challenging session',
                'personal_trainer_assignment_id' => 2,
            ],
            [
                'scheduled_at' => Carbon::now()->subDays(3),
                'status' => 'missed',
                'check_in_time' => '07:00:00',
                'check_out_time' => null,
                'training_log' => null,
                'trainer_notes' => null,
                'member_feedback' => null,
                'personal_trainer_assignment_id' => 3,
            ],
            [
                'scheduled_at' => Carbon::now()->subDays(2),
                'status' => 'completed',
                'check_in_time' => '10:00:00',
                'check_out_time' => '11:00:00',
                'training_log' => json_encode(['yoga' => '60min']),
                'trainer_notes' => 'Excellent flexibility',
                'member_feedback' => 'Loved the session',
                'personal_trainer_assignment_id' => 4,
            ],
            [
                'scheduled_at' => Carbon::now()->subDay(),
                'status' => 'scheduled',
                'check_in_time' => '08:30:00',
                'check_out_time' => null,
                'training_log' => null,
                'trainer_notes' => null,
                'member_feedback' => null,
                'personal_trainer_assignment_id' => 5,
            ],
            [
                'scheduled_at' => Carbon::now(),
                'status' => 'scheduled',
                'check_in_time' => '07:30:00',
                'check_out_time' => null,
                'training_log' => null,
                'trainer_notes' => null,
                'member_feedback' => null,
                'personal_trainer_assignment_id' => 6,
            ],
            [
                'scheduled_at' => Carbon::now()->addDay(),
                'status' => 'scheduled',
                'check_in_time' => '09:15:00',
                'check_out_time' => null,
                'training_log' => null,
                'trainer_notes' => null,
                'member_feedback' => null,
                'personal_trainer_assignment_id' => 7,
            ],
            [
                'scheduled_at' => Carbon::now()->addDays(2),
                'status' => 'scheduled',
                'check_in_time' => '10:00:00',
                'check_out_time' => null,
                'training_log' => null,
                'trainer_notes' => null,
                'member_feedback' => null,
                'personal_trainer_assignment_id' => 8,
            ],
            [
                'scheduled_at' => Carbon::now()->addDays(3),
                'status' => 'scheduled',
                'check_in_time' => '11:00:00',
                'check_out_time' => null,
                'training_log' => null,
                'trainer_notes' => null,
                'member_feedback' => null,
                'personal_trainer_assignment_id' => 9,
            ],
            [
                'scheduled_at' => Carbon::now()->addDays(4),
                'status' => 'scheduled',
                'check_in_time' => '06:45:00',
                'check_out_time' => null,
                'training_log' => null,
                'trainer_notes' => null,
                'member_feedback' => null,
                'personal_trainer_assignment_id' => 10,
            ],
        ];

        foreach ($schedules as $schedule) {
            PersonalTrainerSchedule::create($schedule);
        }
    }
}
