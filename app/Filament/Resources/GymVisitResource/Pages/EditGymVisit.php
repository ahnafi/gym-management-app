<?php

namespace App\Filament\Resources\GymVisitResource\Pages;

use App\Filament\Resources\GymVisitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGymVisit extends EditRecord
{
    protected static string $resource = GymVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
