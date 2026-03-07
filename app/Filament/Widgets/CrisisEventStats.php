<?php

namespace App\Filament\Widgets;

use App\Models\CrisisEvent;
use App\Models\ScreeningSession;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CrisisEventStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make(__('admin.crisis_events_30d'), CrisisEvent::where('created_at', '>=', now()->subDays(30))->count())
                ->description(__('admin.total_crisis_detections'))
                ->color('danger'),
            Stat::make(__('admin.screenings_completed'), ScreeningSession::whereNotNull('completed_at')->count())
                ->description(__('admin.all_time'))
                ->color('success'),
            Stat::make(__('admin.crisis_events_today'), CrisisEvent::whereDate('created_at', today())->count())
                ->description(__('admin.requires_attention'))
                ->color(CrisisEvent::whereDate('created_at', today())->count() > 0 ? 'danger' : 'success'),
        ];
    }
}
