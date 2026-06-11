<?php

namespace App\Filament\Resources\Referrals\Tables;

use App\Enums\ReferralStatus;
use App\Models\Referral;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class ReferralsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('anonymous_id')
                    ->label('Student')
                    ->formatStateUsing(fn (?string $state): string => $state ? substr($state, 0, 8) : '—')
                    ->searchable(),
                TextColumn::make('crisisEvent.severity')
                    ->label('Severity')
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (ReferralStatus $state): string => $state->label())
                    ->color(fn (ReferralStatus $state): string => $state->color()),
                TextColumn::make('referredBy.name')
                    ->label('Referred by')
                    ->placeholder('—'),
                TextColumn::make('scheduled_at')
                    ->dateTime()
                    ->placeholder('Not scheduled')
                    ->label('Appointment'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Raised'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(ReferralStatus::options()),
            ])
            ->recordActions([
                Action::make('schedule')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->color('info')
                    ->visible(fn (Referral $record): bool => in_array($record->status, [ReferralStatus::Pending, ReferralStatus::Scheduled], true))
                    ->schema([
                        DateTimePicker::make('scheduled_at')
                            ->label('Appointment time')
                            ->required()
                            ->seconds(false),
                    ])
                    ->fillForm(fn (Referral $record): array => ['scheduled_at' => $record->scheduled_at])
                    ->action(fn (array $data, Referral $record) => $record->schedule(Carbon::parse($data['scheduled_at']))),
                Action::make('complete')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Referral $record): bool => ! in_array($record->status, [ReferralStatus::Completed, ReferralStatus::Declined], true))
                    ->action(fn (Referral $record) => $record->complete()),
                Action::make('decline')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn (Referral $record): bool => ! in_array($record->status, [ReferralStatus::Completed, ReferralStatus::Declined], true))
                    ->action(fn (Referral $record) => $record->decline()),
            ]);
    }
}
