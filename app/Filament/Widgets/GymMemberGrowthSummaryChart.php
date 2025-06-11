<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class GymMemberGrowthSummaryChart extends ChartWidget
{
    protected static ?string $heading = 'Pertumbuhan Member';

    protected function getData(): array
    {
        // Dummy data: number of new members per month
        $memberData = [25, 40, 32, 50, 47, 60];

        // Generate month labels for the past 6 months
        $months = collect(range(0, 5))->map(function ($i) {
            return now()->subMonths($i)->translatedFormat('F Y');
        })->reverse()->values()->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Pertumbuhan Member',
                    'data' => $memberData,
                    'borderColor' => '#3b82f6', // Tailwind blue-500
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
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
