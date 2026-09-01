<?php

namespace Tests\Feature\Console;

use App\Ai\Agents\DistressAnalyzer;
use Illuminate\Support\Str;
use Tests\TestCase;

class EvaluateDistressClassifierTest extends TestCase
{
    /**
     * @param  array<int, array{string, string}>  $rows
     */
    private function csv(array $rows): string
    {
        $path = tempnam(sys_get_temp_dir(), 'eval').'.csv';
        $handle = fopen($path, 'w');
        fputcsv($handle, ['id', 'text', 'label', 'labeller_notes']);

        foreach ($rows as $index => [$text, $label]) {
            fputcsv($handle, [$index + 1, $text, $label, '']);
        }

        fclose($handle);

        return $path;
    }

    public function test_reports_per_class_recall_against_clinician_labels(): void
    {
        DistressAnalyzer::fake(function (string $prompt): array {
            $severity = match (true) {
                Str::contains($prompt, 'MISSED-MILD') => 'moderate',
                Str::contains($prompt, 'mild') => 'mild',
                Str::contains($prompt, 'severe') => 'severe',
                default => 'minimal',
            };

            return ['severity' => $severity, 'confidence' => 0.9, 'themes' => []];
        });

        $path = $this->csv([
            ['a mild one', 'mild'],
            ['MISSED-MILD text', 'mild'],
            ['clearly severe', 'severe'],
            ['nothing wrong', 'minimal'],
        ]);

        $this->artisan('app:evaluate-distress', ['csv' => $path])
            ->expectsOutputToContain('Agreement: 75.0% (3/4)')
            ->expectsTable(
                ['Class', 'Precision', 'Recall', 'F1', 'n'],
                [
                    ['minimal', '1.00', '1.00', '1.00', 1],
                    ['mild', '1.00', '0.50', '0.67', 2],
                    ['moderate', '0.00', '0.00', '0.00', 0],
                    ['moderately_severe', '0.00', '0.00', '0.00', 0],
                    ['severe', '1.00', '1.00', '1.00', 1],
                    ['Weighted avg', '1.00', '0.75', '0.83', 4],
                ]
            )
            ->assertExitCode(0);
    }

    public function test_writes_per_row_predictions_when_out_is_given(): void
    {
        DistressAnalyzer::fake([['severity' => 'moderate', 'confidence' => 0.8, 'themes' => ['sadness']]]);

        $path = $this->csv([['some text', 'mild']]);
        $out = $path.'.out.csv';

        $this->artisan('app:evaluate-distress', ['csv' => $path, '--out' => $out])->assertExitCode(0);

        $rows = array_map('str_getcsv', file($out, FILE_IGNORE_NEW_LINES));

        $this->assertSame(['id', 'text', 'label', 'predicted', 'confidence', 'themes', 'correct'], $rows[0]);
        $this->assertSame(['1', 'some text', 'mild', 'moderate', '0.8', 'sadness', '0'], $rows[1]);
    }

    public function test_rejects_unknown_labels_before_calling_the_model(): void
    {
        DistressAnalyzer::fake(function (): never {
            $this->fail('The model must not be called when the CSV is invalid.');
        });

        $path = $this->csv([['some text', 'very_bad']]);

        $this->artisan('app:evaluate-distress', ['csv' => $path])
            ->expectsOutputToContain('Row 2: unknown label "very_bad"')
            ->assertExitCode(1);
    }
}
