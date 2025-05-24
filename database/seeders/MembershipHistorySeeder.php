<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MembershipHistory;
use Carbon\Carbon;

class MembershipHistorySeeder extends Seeder
{
    public function run(): void
    {
        $histories = [
            [
                'start_date' => Carbon::now()->subDays(30),
                'end_date' => Carbon::now()->subDays(1),
                'status' => 'expired',
                'user_id' => 1,
                'membership_package_id' => 1,
            ],
            [
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->addDays(20),
                'status' => 'active',
                'user_id' => 2,
                'membership_package_id' => 2,
            ],
            [
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->addDays(15),
                'status' => 'active',
                'user_id' => 3,
                'membership_package_id' => 3,
            ],
            [
                'start_date' => Carbon::now()->subDays(60),
                'end_date' => Carbon::now()->subDays(30),
                'status' => 'expired',
                'user_id' => 4,
                'membership_package_id' => 4,
            ],
            [
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(25),
                'status' => 'active',
                'user_id' => 5,
                'membership_package_id' => 5,
            ],
            [
                'start_date' => Carbon::now()->subDays(40),
                'end_date' => Carbon::now()->subDays(10),
                'status' => 'expired',
                'user_id' => 6,
                'membership_package_id' => 6,
            ],
            [
                'start_date' => Carbon::now()->subDays(3),
                'end_date' => Carbon::now()->addDays(27),
                'status' => 'active',
                'user_id' => 7,
                'membership_package_id' => 1,
            ],
            [
                'start_date' => Carbon::now()->subDays(20),
                'end_date' => Carbon::now()->addDays(10),
                'status' => 'active',
                'user_id' => 8,
                'membership_package_id' => 2,
            ],
            [
                'start_date' => Carbon::now()->subDays(90),
                'end_date' => Carbon::now()->subDays(60),
                'status' => 'expired',
                'user_id' => 9,
                'membership_package_id' => 3,
            ],
            [
                'start_date' => Carbon::now()->subDays(1),
                'end_date' => Carbon::now()->addDays(29),
                'status' => 'active',
                'user_id' => 10,
                'membership_package_id' => 4,
            ],
        ];

        foreach ($histories as $history) {
            MembershipHistory::create($history);
        }
    }
}
