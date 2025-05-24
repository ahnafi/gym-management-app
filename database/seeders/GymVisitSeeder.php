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
                'visit_date' => now()->toDateString(),
                'entry_time' => '08:00:00',
                'exit_time' => '09:30:00',
                'status' => 'left',
                'user_id' => User::where('email', 'member1@example.com')->first()->id,
            ],
            [
                'visit_date' => now()->subDay()->toDateString(),
                'entry_time' => '10:00:00',
                'exit_time' => '11:15:00',
                'status' => 'left',
                'user_id' => User::where('email', 'member2@example.com')->first()->id,
            ],
            [
                'visit_date' => now()->toDateString(),
                'entry_time' => '07:30:00',
                'exit_time' => null,
                'status' => 'in_gym',
                'user_id' => User::where('email', 'member3@example.com')->first()->id,
            ],
            [
                'visit_date' => now()->subDays(2)->toDateString(),
                'entry_time' => '18:00:00',
                'exit_time' => '19:45:00',
                'status' => 'left',
                'user_id' => User::where('email', 'member4@example.com')->first()->id,
            ],
            [
                'visit_date' => now()->toDateString(),
                'entry_time' => '09:00:00',
                'exit_time' => '10:30:00',
                'status' => 'left',
                'user_id' => User::where('email', 'member5@example.com')->first()->id,
            ],
        ];

        foreach ($gymVisits as $visit) {
            GymVisit::create($visit);
        }
    }
}
