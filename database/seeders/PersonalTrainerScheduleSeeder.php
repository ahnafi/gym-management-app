<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PersonalTrainerSchedule;
use App\Models\PersonalTrainerAssignment;
use Carbon\Carbon;

class PersonalTrainerScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil 10 personal trainer assignment secara acak
        $assignments = PersonalTrainerAssignment::inRandomOrder()->take(10)->get();

        $statuses = ['scheduled', 'completed', 'cancelled', 'missed'];

        $schedules = [];

        foreach ($assignments as $assignment) {
            $scheduledAt = Carbon::now()->subDays(rand(0, 30));
            $checkInTime = $scheduledAt->copy()->setTime(rand(6, 10), rand(0, 59));
            $checkOutTime = (rand(0,1) ? $checkInTime->copy()->addHours(rand(1, 3))->addMinutes(rand(0, 59)) : null);
            $status = $checkOutTime ? 'completed' : $statuses[array_rand($statuses)];

            $trainingLog = null;
            $trainerNotes = null;
            $memberFeedback = null;

            if ($status === 'completed') {
                $trainingLog = json_encode([
                    'exercises' => [
                        ['name' => 'Squat', 'reps' => 12, 'sets' => 3],
                        ['name' => 'Bench Press', 'reps' => 10, 'sets' => 3],
                    ],
                    'duration_minutes' => rand(30, 90),
                ]);
                $trainerNotes = "Good progress, needs to focus on form.";
                $memberFeedback = "Felt great after session.";
            } elseif (in_array($status, ['cancelled', 'missed'])) {
                $trainerNotes = "Session was " . $status . ".";
                $memberFeedback = null;
            }

            $schedules[] = [
                'scheduled_at' => $scheduledAt->format('Y-m-d'),
                'status' => $status,
                'check_in_time' => $checkInTime->format('H:i:s'),
                'check_out_time' => $checkOutTime ? $checkOutTime->format('H:i:s') : null,
                'training_log' => $trainingLog,
                'trainer_notes' => $trainerNotes,
                'member_feedback' => $memberFeedback,
                'personal_trainer_assignment_id' => $assignment->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        PersonalTrainerSchedule::insert($schedules);
    }
}
