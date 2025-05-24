<?php

namespace App\Filament\Resources\MembershipHistoryResource\Pages;

use App\Filament\Resources\MembershipHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMembershipHistory extends CreateRecord
{
    protected static string $resource = MembershipHistoryResource::class;

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
