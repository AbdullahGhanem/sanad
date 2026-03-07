<?php

namespace App\Filament\Widgets;

use App\Models\ScreeningSession;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ScreeningTrendsChart extends ChartWidget
{
    public function getHeading(): ?string
    {
        return __('admin.screening_trends');
    }

    protected static ?int $sort = 3;

    protected ?string $pollingInterval = '30s';

    protected function getData(): array
    {
        $days = collect(range(29, 0))->map(fn ($daysAgo) => Carbon::today()->subDays($daysAgo));

        $screenings = ScreeningSession::query()
            ->whereNotNull('completed_at')
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupByRaw('DATE(created_at)')
            ->pluck('count', 'date');

        return [
            'datasets' => [
                [
                    'label' => __('admin.completed_screenings_label'),
                    'data' => $days->map(fn ($day) => $screenings->get($day->format('Y-m-d'), 0))->toArray(),
                    'borderColor' => '#0d9488',
                    'backgroundColor' => 'rgba(13, 148, 136, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $days->map(fn ($day) => $day->format('M d'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
