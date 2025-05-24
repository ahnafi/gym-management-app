<?php

namespace App\Filament\Resources\GymClassScheduleResource\Pages;

use App\Filament\Resources\GymClassScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGymClassSchedule extends EditRecord
{
    protected static string $resource = GymClassScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
