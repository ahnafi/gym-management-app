<?php

namespace App\Services;

use App\Models\MembershipHistory;
use App\Models\GymClass;
use App\Models\PersonalTrainer;
use App\Models\MembershipPackage;
use App\Models\PersonalTrainerPackage;
use App\Models\GymClassAttendance;
use App\Models\PersonalTrainerAssignment;
use App\Models\PersonalTrainerSchedule;
use App\Models\GymClassSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AssignmentService
{
    public static function updateMembership($userId, $membershipPackageId)
    {
        $membershipPackage = MembershipPackage::findOrFail($membershipPackageId);
        $user = User::findOrFail($userId);

        if ($membershipPackage->code === 'MP-001') {
            $user->membership_registered = 'registered';
            $user->save();
            return;
        }

        $last_membership_end_date = $user->membership_end_date;
        $day_of_membership = $membershipPackage->duration;

        $membershipHistory = new MembershipHistory();
        $membershipHistory->membership_package_id = $membershipPackageId;
        $membershipHistory->user_id = $userId;

        $now = Carbon::now();

        if (!$last_membership_end_date || $last_membership_end_date < $now) {
            $membershipHistory->start_date = $now;
            $user->membership_status = 'active';
        } else {
            $membershipHistory->start_date = $last_membership_end_date->copy()->addDay();
        }

        $membershipHistory->end_date = $membershipHistory->start_date->copy()->addDays($day_of_membership);

        $user->membership_end_date = $membershipHistory->end_date;

        DB::transaction(function () use ($user, $membershipHistory) {
            $user->save();
            $membershipHistory->save();
        });
    }

    public static function assignGymClass($userId, $gymClassId, $gymClassScheduleId)
    {
        try {
            return DB::transaction(function () use ($userId, $gymClassId, $gymClassScheduleId) {
                $user = User::findOrFail($userId);
                $gymClass = GymClass::findOrFail($gymClassId);
                $schedule = GymClassSchedule::lockForUpdate()->findOrFail($gymClassScheduleId);

                if ($schedule->available_slot <= 0) {
                    throw new \Exception('No available slots in this class schedule');
                }

                $attendance = GymClassAttendance::create([
                    'user_id' => $user->id,
                    'gym_class_schedule_id' => $schedule->id,
                    'attendance_date' => $schedule->class_date,
                ]);

                $schedule->decrement('available_slot');

                return $attendance;
            });
        } catch (\Exception $e) {
            Log::error('Gym class assignment failed: ' . $e->getMessage(), [
                'user_id' => $userId,
                'class_id' => $gymClassId,
                'schedule_id' => $gymClassScheduleId
            ]);
            throw $e;
        }
    }

    public static function assignPersonalTrainer($userId, $personalTrainerPackageId)
    {
        DB::transaction(function () use ($userId, $personalTrainerPackageId) {
            $user = User::findOrFail($userId);
            $package = PersonalTrainerPackage::findOrFail($personalTrainerPackageId);
            $trainer = PersonalTrainer::findOrFail($package->personal_trainer_id);

            $assignment = PersonalTrainerAssignment::create([
                'day_left' => $package->day_duration,
                'start_date' => Carbon::now(),
                'user_id' => $user->id,
                'personal_trainer_id' => $trainer->id,
                'personal_trainer_package_id' => $package->id,
                'status' => 'active'
            ]);
        });
    }
}
