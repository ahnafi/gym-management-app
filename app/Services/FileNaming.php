<?php

namespace App\Services;

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
        return 'gym-up-' . $user_id . '-' . now()->format('YmdHis') . '.' . $extension;
    }

    public static function generateMembershipPackageName($membershipPackage_id, $extension): string
    {
        if ($membershipPackage_id != -1) {
            $code = MembershipPackage::find($membershipPackage_id)?->code ?? 'UNKNOWN';
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
        return 'gym-gc-' . $gymClass_id . '-' . now()->format('YmdHis') . '.' . $extension;
    }

    public static function generatePersonalTrainerName($personal_trainer_id, $extension): string
    {
        return 'gym-pt-' . $personal_trainer_id . '-' . now()->format('YmdHis') . '.' . $extension;
    }

    public static function generatePackageName($personal_trainer_package_id, $extension): string
    {
        return 'gym-ptp-' . $personal_trainer_package_id . '-' . now()->format('YmdHis') . '.' . $extension;
    }
}
