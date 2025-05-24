<?php

namespace App\Filament\Resources\PersonalTrainerAssignmentResource\Pages;

use App\Filament\Resources\PersonalTrainerAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPersonalTrainerAssignment extends EditRecord
{
    protected static string $resource = PersonalTrainerAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
