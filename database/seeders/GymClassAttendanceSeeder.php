<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GymClassAttendance;
use App\Models\User;
use App\Models\GymClassSchedule;
use Carbon\Carbon;

class GymClassAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil 10 user dengan role 'member'
        $members = User::where('role', 'member')->inRandomOrder()->take(10)->get();

        // Ambil semua jadwal gym class
        $schedules = GymClassSchedule::pluck('id')->toArray();

        // Status acak untuk variasi
        $statuses = ['assigned', 'attended', 'missed', 'cancelled'];

        $attendances = [];

        foreach ($members as $member) {
            $attendances[] = [
                'status' => $statuses[array_rand($statuses)],
                'attended_at' => Carbon::now()->subDays(rand(1, 10))->format('Y-m-d H:i:s'),
                'user_id' => $member->id,
                'gym_class_schedule_id' => $schedules[array_rand($schedules)],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        GymClassAttendance::insert($attendances);
    }
}
