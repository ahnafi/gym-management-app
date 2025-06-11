<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class GymRevenueGrowthSummaryChart extends ChartWidget
{
    protected static ?string $heading = 'Pertumbuhan Pendapatan';

    protected function getData(): array
    {
        // Generate dummy revenue data (in IDR)
        $revenueData = [1500000, 2300000, 1800000, 2500000, 2700000, 3000000];

        // Generate month labels for the past 6 months
        $months = collect(range(0, 5))->map(function ($i) {
            return now()->subMonths($i)->translatedFormat('F Y');
        })->reverse()->values()->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan Bulanan (Rp)',
                    'data' => $revenueData,
                    'borderColor' => '#10b981', // Tailwind green-500
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
