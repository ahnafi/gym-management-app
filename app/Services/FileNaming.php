<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\MembershipPackage;
use App\Models\GymClass;
use App\Models\PersonalTrainer;
use Illuminate\Support\Facades\DB;
use App\Models\PersonalTrainerPackage;

class FileNaming
{
    public static function generateUserProfileName($user_id, $extension): string
    {
        if ($user_id != -1) {
            $code = 'UP-' . str_pad($user_id, 3, '0', STR_PAD_LEFT);
        } else {
            $nextId = DB::table('INFORMATION_SCHEMA.TABLES')
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', 'users')
                ->value('AUTO_INCREMENT');

            $code = 'UP-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        }

        $uuid = Str::uuid()->toString();
        $shortUuid = substr(str_replace('-', '', $uuid), 0, 6);

        return 'GYM-' . $code . '-' . $shortUuid . '-' . now()->format('YmdHis') . '.' . $extension;
    }

    public static function generateMembershipPackageName($membershipPackage_id, $extension): string
    {
        if ($membershipPackage_id != -1) {
            $code = MembershipPackage::find($membershipPackage_id)?->code;
        } else {
            $nextId = DB::table('INFORMATION_SCHEMA.TABLES')
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', 'membership_packages')
                ->value('AUTO_INCREMENT');

            $code = 'MP-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        }

        $uuid = Str::uuid()->toString();
        $shortUuid = substr(str_replace('-', '', $uuid), 0, 6);

        return 'GYM-' . $code . '-' . $shortUuid . '-' . now()->format('YmdHis') . '.' . $extension;
    }

    public static function generateGymClassName($gymClass_id, $extension): string
    {
        if ($gymClass_id != -1) {
            $code = GymClass::find($gymClass_id)?->code;
        } else {
            $nextId = DB::table('INFORMATION_SCHEMA.TABLES')
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', 'gym_classes')
                ->value('AUTO_INCREMENT');

            $code = 'GC-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        }

        $uuid = Str::uuid()->toString();
        $shortUuid = substr(str_replace('-', '', $uuid), 0, 6);

        return 'GYM-' . $code . '-' . $shortUuid . '-' . now()->format('YmdHis') . '.' . $extension;
    }

    public static function generatePersonalTrainerName($personal_trainer_id, $extension): string
    {
        if ($personal_trainer_id != -1) {
            $code = PersonalTrainer::find($personal_trainer_id)?->code;
        } else {
            $nextId = DB::table('INFORMATION_SCHEMA.TABLES')
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', 'personal_trainers')
                ->value('AUTO_INCREMENT');

            $code = 'PT-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        }

        $uuid = Str::uuid()->toString();
        $shortUuid = substr(str_replace('-', '', $uuid), 0, 6);

        return 'GYM-' . $code . '-' . $shortUuid . '-' . now()->format('YmdHis') . '.' . $extension;
    }

    public static function generatePersonalTrainerPackageName($personal_trainer_package_id, $extension): string
    {
        if ($personal_trainer_package_id != -1) {
            $code = PersonalTrainerPackage::find($personal_trainer_package_id)?->code;
        } else {
            $nextId = DB::table('INFORMATION_SCHEMA.TABLES')
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', 'personal_trainer_packages')
                ->value('AUTO_INCREMENT');

            $code = 'PTP-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        }

        $uuid = Str::uuid()->toString();
        $shortUuid = substr(str_replace('-', '', $uuid), 0, 6);

        return 'GYM-' . $code . '-' . $shortUuid . '-' . now()->format('YmdHis') . '.' . $extension;
    }
}
