<?php

namespace App\Filament\Resources\GymVisitResource\Pages;

use App\Filament\Resources\GymVisitResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGymVisit extends CreateRecord
{
    protected static string $resource = GymVisitResource::class;

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
