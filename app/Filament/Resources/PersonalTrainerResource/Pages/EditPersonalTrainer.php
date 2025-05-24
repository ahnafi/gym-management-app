<?php

namespace App\Filament\Resources\PersonalTrainerResource\Pages;

use App\Filament\Resources\PersonalTrainerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPersonalTrainer extends EditRecord
{
    protected static string $resource = PersonalTrainerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
