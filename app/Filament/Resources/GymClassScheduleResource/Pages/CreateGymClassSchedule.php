<?php

namespace App\Filament\Resources\GymClassScheduleResource\Pages;

use App\Filament\Resources\GymClassScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGymClassSchedule extends CreateRecord
{
    protected static string $resource = GymClassScheduleResource::class;

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
