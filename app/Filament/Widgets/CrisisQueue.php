<?php

namespace App\Filament\Widgets;

use App\Enums\CrisisStatus;
use App\Filament\Resources\CrisisEvents\Tables\CrisisEventsTable;
use App\Models\CrisisEvent;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class CrisisQueue extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function getTableHeading(): string
    {
        return __('admin.crisis_queue');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => CrisisEvent::query()->unresolved()->with('handledBy')->withCount('notes'))
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->columns([
                TextColumn::make('source')
                    ->badge(),
                TextColumn::make('severity')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'high' => 'danger',
                        'moderate' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (CrisisStatus $state): string => $state->label())
                    ->color(fn (CrisisStatus $state): string => $state->color()),
                TextColumn::make('handledBy.name')
                    ->label('Handled by')
                    ->placeholder('Unassigned'),
                TextColumn::make('notes_count')
                    ->label('Notes')
                    ->badge(),
                TextColumn::make('created_at')
                    ->since()
                    ->label('Detected'),
            ])
            ->recordActions(CrisisEventsTable::triageActions());
    }
}
