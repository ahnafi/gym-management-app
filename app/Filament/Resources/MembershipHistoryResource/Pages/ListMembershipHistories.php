<?php

namespace App\Filament\Resources\MembershipHistoryResource\Pages;

use App\Filament\Resources\MembershipHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMembershipHistories extends ListRecords
{
    protected static string $resource = MembershipHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
