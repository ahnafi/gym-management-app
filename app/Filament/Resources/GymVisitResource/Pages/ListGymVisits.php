<?php

namespace App\Filament\Resources\GymVisitResource\Pages;

use App\Filament\Resources\GymVisitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGymVisits extends ListRecords
{
    protected static string $resource = GymVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
