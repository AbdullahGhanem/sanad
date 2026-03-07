<?php

namespace App\Filament\Widgets;

use App\Models\ScreeningSession;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ScreeningStats extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $totalStudents = User::where('role', 'student')->count();
        $screeningsThisWeek = ScreeningSession::whereNotNull('completed_at')
            ->where('created_at', '>=', now()->subWeek())
            ->count();
        $severeCases = ScreeningSession::whereNotNull('completed_at')
            ->whereIn('combined_severity', ['severe', 'moderately_severe'])
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        return [
            Stat::make(__('admin.registered_students'), $totalStudents)
                ->description(__('admin.total_student_accounts'))
                ->color('primary'),
            Stat::make(__('admin.screenings_this_week'), $screeningsThisWeek)
                ->description(__('admin.completed_screenings'))
                ->color('info'),
            Stat::make(__('admin.high_severity_30d'), $severeCases)
                ->description(__('admin.moderately_severe_or_severe'))
                ->color($severeCases > 0 ? 'warning' : 'success'),
        ];
    }
}
