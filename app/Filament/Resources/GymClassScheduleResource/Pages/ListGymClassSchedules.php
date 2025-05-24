<?php

namespace App\Filament\Resources\GymClassScheduleResource\Pages;

use App\Filament\Resources\GymClassScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGymClassSchedules extends ListRecords
{
    protected static string $resource = GymClassScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
