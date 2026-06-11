<?php

namespace App\Filament\Widgets;

use App\Enums\CrisisStatus;
use App\Models\CrisisEvent;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CrisisTriageStats extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $open = CrisisEvent::query()->where('status', CrisisStatus::Open->value)->count();
        $acknowledged = CrisisEvent::query()->where('status', CrisisStatus::Acknowledged->value)->count();
        $resolvedThisWeek = CrisisEvent::query()
            ->where('status', CrisisStatus::Resolved->value)
            ->where('handled_at', '>=', now()->subWeek())
            ->count();

        return [
            Stat::make(__('admin.crisis_open'), $open)
                ->description(__('admin.crisis_open_hint'))
                ->color($open > 0 ? 'danger' : 'success'),
            Stat::make(__('admin.crisis_in_progress'), $acknowledged)
                ->description(__('admin.crisis_in_progress_hint'))
                ->color('warning'),
            Stat::make(__('admin.crisis_resolved_week'), $resolvedThisWeek)
                ->description(__('admin.crisis_resolved_week_hint'))
                ->color('success'),
        ];
    }
}
