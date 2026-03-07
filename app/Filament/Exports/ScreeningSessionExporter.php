<?php

namespace App\Filament\Exports;

use App\Models\ScreeningSession;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class ScreeningSessionExporter extends Exporter
{
    protected static ?string $model = ScreeningSession::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('Session ID'),
            ExportColumn::make('anonymous_id')
                ->label('Anonymous ID'),
            ExportColumn::make('phq9_score')
                ->label('PHQ-9 Score'),
            ExportColumn::make('gad7_score')
                ->label('GAD-7 Score'),
            ExportColumn::make('combined_severity')
                ->label('Severity'),
            ExportColumn::make('nlp_classification')
                ->label('NLP Classification'),
            ExportColumn::make('completed_at')
                ->label('Completed At'),
            ExportColumn::make('created_at')
                ->label('Created At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your screening session export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
