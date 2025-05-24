<?php

namespace App\Filament\Resources\PersonalTrainerPackageResource\Pages;

use App\Filament\Resources\PersonalTrainerPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPersonalTrainerPackage extends EditRecord
{
    protected static string $resource = PersonalTrainerPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
