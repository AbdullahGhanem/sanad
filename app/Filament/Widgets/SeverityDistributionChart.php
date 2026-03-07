<?php

namespace App\Filament\Widgets;

use App\Models\ScreeningSession;
use Filament\Widgets\ChartWidget;

class SeverityDistributionChart extends ChartWidget
{
    public function getHeading(): ?string
    {
        return __('admin.severity_distribution');
    }

    protected static ?int $sort = 4;

    protected ?string $pollingInterval = '30s';

    protected function getData(): array
    {
        $distribution = ScreeningSession::query()
            ->whereNotNull('completed_at')
            ->whereNotNull('combined_severity')
            ->selectRaw('combined_severity, COUNT(*) as count')
            ->groupBy('combined_severity')
            ->pluck('count', 'combined_severity');

        $labels = ['minimal', 'mild', 'moderate', 'moderately_severe', 'severe'];
        $colors = ['#9ca3af', '#06b6d4', '#f59e0b', '#f97316', '#ef4444'];

        return [
            'datasets' => [
                [
                    'data' => collect($labels)->map(fn ($label) => $distribution->get($label, 0))->toArray(),
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => [
                __('admin.severity_minimal'),
                __('admin.severity_mild'),
                __('admin.severity_moderate'),
                __('admin.severity_moderately_severe'),
                __('admin.severity_severe'),
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
