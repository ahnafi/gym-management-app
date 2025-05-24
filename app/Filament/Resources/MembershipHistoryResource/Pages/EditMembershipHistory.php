<?php

namespace App\Filament\Resources\MembershipHistoryResource\Pages;

use App\Filament\Resources\MembershipHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMembershipHistory extends EditRecord
{
    protected static string $resource = MembershipHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
