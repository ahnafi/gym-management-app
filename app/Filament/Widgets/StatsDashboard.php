<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Transaction;
use App\Models\GymClass;
use App\Models\GymVisit;
use App\Models\PersonalTrainer;
use App\Models\MembershipHistory;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class StatsDashboard extends BaseWidget
{
    protected function getStats(): array
    {
        $totalUsers = Cache::remember('stats.total_users', now()->addMinutes(10), fn () =>
        User::count()
        );

        $activeMembers = Cache::remember('stats.active_members', now()->addMinutes(10), fn () =>
        User::where('membership_status', 'active')->count()
        );

        $monthlyRevenue = Cache::remember('stats.monthly_revenue', now()->addMinutes(10), fn () =>
        Transaction::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('payment_status', 'paid')
            ->sum('amount')
        );

        $activeClasses = Cache::remember('stats.active_classes', now()->addMinutes(10), fn () =>
        GymClass::where('status', 'active')->count()
        );

        $visitsToday = Cache::remember('stats.visits_today', now()->addMinutes(10), fn () =>
        GymVisit::whereDate('visit_date', today())->count()
        );

        $activeTrainers = Cache::remember('stats.active_trainers', now()->addMinutes(10), fn () =>
        PersonalTrainer::whereHas('userPersonalTrainer', function ($q) {
            $q->where('role', 'trainer');
        })->count()
        );

        return [
            Stat::make('Total Pengguna', $totalUsers)
                ->description('Jumlah seluruh pengguna yang terdaftar')
                ->icon('heroicon-o-user-group'),

            Stat::make('Member Aktif', $activeMembers)
                ->description('Member yang sedang aktif berlangganan')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Pendapatan Bulan Ini', Number::currency($monthlyRevenue, 'IDR'))
                ->description('Total pendapatan dari transaksi bulan ini')
                ->icon('heroicon-o-currency-dollar')
                ->color('info'),

            Stat::make('Kelas Aktif', $activeClasses)
                ->description('Jumlah kelas gym yang aktif')
                ->icon('heroicon-o-rectangle-stack')
                ->color('warning'),

            Stat::make('Kunjungan Hari Ini', $visitsToday)
                ->description('Jumlah total kunjungan gym hari ini')
                ->icon('heroicon-o-calendar')
                ->color('primary'),

            Stat::make('Trainer Aktif', $activeTrainers)
                ->description('Total personal trainer yang aktif')
                ->icon('heroicon-o-user-circle')
                ->color('secondary'),
        ];
    }
}
