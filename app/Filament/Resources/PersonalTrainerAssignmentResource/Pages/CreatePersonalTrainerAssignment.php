<?php

namespace App\Filament\Resources\PersonalTrainerAssignmentResource\Pages;

use App\Filament\Resources\PersonalTrainerAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePersonalTrainerAssignment extends CreateRecord
{
    protected static string $resource = PersonalTrainerAssignmentResource::class;

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
