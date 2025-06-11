<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationTitle = 'Dashboard';
    protected static ?string $label = 'Dashboard';

    public function getActiveMemberCount()
    {
        $activeMembers = User::where('membership_status', 'active')->count();

    }
}
