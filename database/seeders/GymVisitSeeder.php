<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GymVisit;
use App\Models\User;

class GymVisitSeeder extends Seeder
{
    public function run(): void
    {
        $gymVisits = [
            [
                'visit_date' => now()->subDay()->toDateString(),
                'entry_time' => '10:00:00',
                'exit_time' => '11:15:00',
                'status' => 'left',
                'user_id' => User::where('email', 'admin@example.com')->first()->id,
            ],
            [
                'visit_date' => now()->subDay(100)->toDateString(),
                'entry_time' => '10:00:00',
                'exit_time' => '12:15:00',
                'status' => 'left',
                'user_id' => User::where('email', 'admin@example.com')->first()->id,
            ],
            [
                'visit_date' => now()->toDateString(),
                'entry_time' => '07:30:00',
                'exit_time' => null,
                'status' => 'in_gym',
                'user_id' => User::where('email', 'admin@example.com')->first()->id,
            ],
            [
                'visit_date' => now()->subDays(2)->toDateString(),
                'entry_time' => '18:00:00',
                'exit_time' => '19:45:00',
                'status' => 'left',
                'user_id' => User::where('email', 'admin@example.com')->first()->id,
            ],
            [
                'visit_date' => now()->subDays(2)->toDateString(),
                'entry_time' => '18:00:00',
                'exit_time' => '19:45:00',
                'status' => 'left',
                'user_id' => User::where('email', 'admin@example.com')->first()->id,
            ],
            [
                'visit_date' => now()->toDateString(),
                'entry_time' => '19:30:00',
                'exit_time' => null,
                'status' => 'in_gym',
                'user_id' => User::where('email', 'trainer1@example.com')->first()->id,
            ],
            [
                'visit_date' => now()->toDateString(),
                'entry_time' => '12:00:00',
                'exit_time' => '15:30:00',
                'status' => 'left',
                'user_id' => User::where('email', 'trainer1@example.com')->first()->id,
            ],
            [
                'visit_date' => now()->subDay(2)->toDateString(),
                'entry_time' => '07:00:00',
                'exit_time' => '07:20:00',
                'status' => 'left',
                'user_id' => User::where('email', 'trainer1@example.com')->first()->id,
            ],
            [
                'visit_date' => now()->subDay(9)->toDateString(),
                'entry_time' => '08:00:00',
                'exit_time' => '10:50:00',
                'status' => 'left',
                'user_id' => User::where('email', 'trainer1@example.com')->first()->id,
            ],
            [
                'visit_date' => now()->subDay(12)->toDateString(),
                'entry_time' => '16:00:00',
                'exit_time' => '17:00:00',
                'status' => 'left',
                'user_id' => User::where('email', 'trainer1@example.com')->first()->id,
            ],
            [
                'visit_date' => now()->toDateString(),
                'entry_time' => '19:30:00',
                'exit_time' => null,
                'status' => 'in_gym',
                'user_id' => User::where('email', 'member@example.com')->first()->id,
            ],
            [
                'visit_date' => now()->toDateString(),
                'entry_time' => '09:00:00',
                'exit_time' => '10:30:00',
                'status' => 'left',
                'user_id' => User::where('email', 'member@example.com')->first()->id,
            ],
            [
                'visit_date' => now()->toDateString(),
                'entry_time' => '12:00:00',
                'exit_time' => '12:30:00',
                'status' => 'left',
                'user_id' => User::where('email', 'member@example.com')->first()->id,
            ],
            [
                'visit_date' => now()->subDay(9)->toDateString(),
                'entry_time' => '08:00:00',
                'exit_time' => '10:30:00',
                'status' => 'left',
                'user_id' => User::where('email', 'member@example.com')->first()->id,
            ],
        ];

        foreach ($gymVisits as $visit) {
            GymVisit::create($visit);
        }
    }
}
