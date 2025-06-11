<?php

namespace App\Filament\PersonalTrainer\Resources\PersonalTrainerAssignmentResource\Pages;

use App\Filament\PersonalTrainer\Resources\PersonalTrainerAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPersonalTrainerAssignments extends ListRecords
{
    protected static string $resource = PersonalTrainerAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
